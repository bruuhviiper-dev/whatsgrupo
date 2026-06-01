<?php

namespace App\Services;

use App\Models\BoostOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Integração com o Mercado Pago para cobranças via PIX.
 *
 * Em modo local (sem token real): retorna dados simulados.
 * Em produção: utiliza a API REST do Mercado Pago v1.
 *
 * Documentação: https://www.mercadopago.com.br/developers/pt/reference/payments
 */
class MercadoPagoPaymentService
{
    protected const BASE_URL = 'https://api.mercadopago.com';

    protected string $accessToken;
    protected string $secretKey;

    public function __construct()
    {
        $this->accessToken = config('mercadopago.access_token', '');
        $this->secretKey   = config('mercadopago.secret_key', '');
    }

    // -------------------------------------------------------------------------
    // Modo de simulação
    // -------------------------------------------------------------------------

    protected function isLocalMode(): bool
    {
        return empty($this->accessToken)
            || str_contains($this->accessToken, 'placeholder')
            || $this->accessToken === 'APP_USR-placeholder';
    }

    // -------------------------------------------------------------------------
    // Headers padrão
    // -------------------------------------------------------------------------

    protected function headers(string $idempotencyKey = ''): array
    {
        return array_filter([
            'Authorization'     => 'Bearer ' . $this->accessToken,
            'Content-Type'      => 'application/json',
            'X-Idempotency-Key' => $idempotencyKey ?: uniqid('wg-mp-', true),
        ]);
    }

    // -------------------------------------------------------------------------
    // Criação de cobrança PIX
    // -------------------------------------------------------------------------

    /**
     * Cria uma cobrança PIX via Mercado Pago.
     *
     * Retorna:
     *   - payment_id : ID numérico da cobrança no MP
     *   - qr_code    : data:image/png;base64,... para exibir o QR
     *   - copy_paste : código PIX copia e cola (EMV / BR Code)
     *
     * @param  BoostOrder  $order
     * @param  string      $cpf    CPF do pagador (opcional, mas recomendado pelo MP)
     */
    public function createPixCharge(BoostOrder $order, string $cpf = ''): array
    {
        if ($this->isLocalMode()) {
            Log::info('[MercadoPago] Modo simulação — PIX fictício gerado.');
            return $this->simulatedPixResponse($order);
        }

        try {
            // Extrai nome e sobrenome
            $parts     = explode(' ', trim($order->buyer_name), 2);
            $firstName = $parts[0];
            $lastName  = $parts[1] ?? $firstName;

            // Monta dados do pagador
            $payer = [
                'email'      => $order->buyer_email,
                'first_name' => $firstName,
                'last_name'  => $lastName,
            ];

            $cpfClean = preg_replace('/\D/', '', $cpf);
            if (strlen($cpfClean) === 11) {
                $payer['identification'] = [
                    'type'   => 'CPF',
                    'number' => $cpfClean,
                ];
            }

            $payload = [
                'transaction_amount' => (float) $order->amount,
                'description'        => "Pacote VIP WhatsGrupos — Pedido #{$order->id}",
                'payment_method_id'  => 'pix',
                'external_reference' => (string) $order->id,
                'date_of_expiration' => now('America/Sao_Paulo')->addHours(24)->format('Y-m-d\TH:i:s.000-03:00'),
                'payer'              => $payer,
            ];

            $response = Http::withHeaders($this->headers("wg-{$order->id}-" . time()))
                ->post(self::BASE_URL . '/v1/payments', $payload);

            if (! $response->successful()) {
                Log::error('[MercadoPago] Falha ao criar PIX. Status: ' . $response->status() . ' Body: ' . $response->body());
                throw new \RuntimeException('Falha ao criar pagamento PIX no Mercado Pago.');
            }

            $data   = $response->json();
            $txData = $data['point_of_interaction']['transaction_data'] ?? [];

            if (empty($txData['qr_code'])) {
                throw new \RuntimeException('Mercado Pago não retornou QR Code PIX.');
            }

            return [
                'payment_id' => (string) $data['id'],
                'qr_code'    => 'data:image/png;base64,' . ($txData['qr_code_base64'] ?? ''),
                'copy_paste' => $txData['qr_code'] ?? '',
            ];

        } catch (\Exception $e) {
            Log::error('[MercadoPago] Erro ao criar PIX: ' . $e->getMessage());
            return $this->simulatedPixResponse($order);
        }
    }

    // -------------------------------------------------------------------------
    // Consulta de status
    // -------------------------------------------------------------------------

    /**
     * Consulta o status de um pagamento no Mercado Pago.
     * Retorna 'pending', 'paid' ou 'failed'.
     *
     * Mapeamento de status MP → interno:
     *   approved                → paid
     *   rejected, cancelled...  → failed
     *   pending, in_process...  → pending
     */
    public function getChargeStatus(string $paymentId): string
    {
        if ($this->isLocalMode() || empty($paymentId) || str_starts_with($paymentId, 'MP-SIM-')) {
            return 'pending';
        }

        try {
            $response = Http::withHeaders($this->headers())
                ->get(self::BASE_URL . "/v1/payments/{$paymentId}");

            if (! $response->successful()) {
                return 'pending';
            }

            $status = $response->json('status');

            return match ($status) {
                'approved'                                  => 'paid',
                'rejected', 'cancelled', 'refunded',
                'charged_back'                              => 'failed',
                default                                     => 'pending',
            };

        } catch (\Exception $e) {
            Log::error('[MercadoPago] Erro ao consultar status: ' . $e->getMessage());
            return 'pending';
        }
    }

    // -------------------------------------------------------------------------
    // Webhook
    // -------------------------------------------------------------------------

    /**
     * Verifica a autenticidade do webhook do Mercado Pago usando HMAC-SHA256.
     *
     * Header X-Signature: ts={timestamp},v1={signature}
     * Mensagem assinada : id:{data.id};request-id:{X-Request-Id};ts:{ts}
     */
    public function verifyWebhook(Request $request): bool
    {
        if (empty($this->secretKey)) {
            return true;
        }

        $xSignature = $request->header('x-signature', '');
        $xRequestId = $request->header('x-request-id', '');

        if (empty($xSignature)) {
            return false;
        }

        // Extrai ts e v1 do header
        $parts = [];
        foreach (explode(',', $xSignature) as $part) {
            $kv = explode('=', trim($part), 2);
            if (count($kv) === 2) {
                $parts[trim($kv[0])] = trim($kv[1]);
            }
        }

        $ts = $parts['ts'] ?? '';
        $v1 = $parts['v1'] ?? '';

        if (empty($ts) || empty($v1)) {
            return false;
        }

        $dataId  = (string) $request->input('data.id', '');
        $message = "id:{$dataId};request-id:{$xRequestId};ts:{$ts}";
        $computed = hash_hmac('sha256', $message, $this->secretKey);

        return hash_equals($computed, $v1);
    }

    /**
     * Processa o payload do webhook do Mercado Pago.
     * Retorna array com payment_id e status, ou null se evento irrelevante.
     */
    public function processWebhook(array $payload): ?array
    {
        $type   = $payload['type'] ?? '';
        $action = $payload['action'] ?? '';

        // Só processa notificações de pagamento
        if ($type !== 'payment') {
            return null;
        }

        $paymentId = (string) ($payload['data']['id'] ?? '');

        if (empty($paymentId)) {
            return null;
        }

        $status = $this->getChargeStatus($paymentId);

        return [
            'payment_id' => $paymentId,
            'status'     => $status,
        ];
    }

    // -------------------------------------------------------------------------
    // Simulação local
    // -------------------------------------------------------------------------

    protected function simulatedPixResponse(BoostOrder $order): array
    {
        $fakeSvg = '<svg xmlns="http://www.w3.org/2000/svg" width="200" height="200">'
            . '<rect width="200" height="200" fill="white"/>'
            . '<rect x="10" y="10" width="40" height="40" fill="black"/>'
            . '<rect x="150" y="10" width="40" height="40" fill="black"/>'
            . '<rect x="10" y="150" width="40" height="40" fill="black"/>'
            . '<text x="100" y="100" text-anchor="middle" font-size="9" fill="black">MERCADO PAGO</text>'
            . '<text x="100" y="115" text-anchor="middle" font-size="8" fill="#009EE3">PIX SIMULADO</text>'
            . '</svg>';

        $fakeQrCodeBase64 = 'data:image/svg+xml;base64,' . base64_encode($fakeSvg);

        $fakeCopyPaste = '00020126580014br.gov.bcb.pix0136'
            . strtoupper(str_pad((string) $order->id, 8, '0', STR_PAD_LEFT))
            . 'MPPIX52040000530398654'
            . number_format((float) $order->amount, 2, '.', '')
            . '5802BR5913WhatsGrupos6009SAO PAULO62070503***6304'
            . strtoupper(substr(md5($order->id . 'mp' . $order->amount), 0, 4));

        return [
            'payment_id' => 'MP-SIM-' . strtoupper(uniqid()),
            'qr_code'    => $fakeQrCodeBase64,
            'copy_paste' => $fakeCopyPaste,
        ];
    }
}
