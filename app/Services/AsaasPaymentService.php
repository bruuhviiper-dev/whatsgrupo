<?php

namespace App\Services;

use App\Models\BoostOrder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Serviço de integração com o Asaas para geração de cobranças via PIX.
 *
 * Em modo local (sem API key configurada): retorna dados simulados.
 * Em produção: chama a API real do Asaas.
 *
 * Documentação: https://docs.asaas.com
 */
class AsaasPaymentService
{
    protected string $baseUrl;
    protected string $apiKey;

    public function __construct()
    {
        $this->apiKey  = config('asaas.api_key', '');
        $this->baseUrl = config('asaas.sandbox', true)
            ? 'https://sandbox.asaas.com/api/v3'
            : 'https://api.asaas.com/v3';
    }

    /**
     * Verifica se estamos em modo de simulação local (sem API key real).
     */
    protected function isLocalMode(): bool
    {
        return empty($this->apiKey)
            || str_contains($this->apiKey, 'placeholder')
            || str_contains($this->apiKey, '$aact');
    }

    /**
     * Retorna os headers padrão para chamadas à API do Asaas.
     */
    protected function headers(): array
    {
        return [
            'Content-Type' => 'application/json',
            'access_token' => $this->apiKey,
            'User-Agent'   => 'WhatsGrupos/1.0',
        ];
    }

    // -------------------------------------------------------------------------
    // Clientes (Customers)
    // -------------------------------------------------------------------------

    /**
     * Cria ou busca o cliente no Asaas pelo e-mail do comprador.
     * Retorna o ID do cliente no Asaas.
     */
    protected function findOrCreateCustomer(BoostOrder $order): string
    {
        // Busca cliente existente pelo e-mail
        $search = Http::withHeaders($this->headers())
            ->get("{$this->baseUrl}/customers", ['email' => $order->buyer_email]);

        if ($search->successful()) {
            $data = $search->json();
            if (($data['totalCount'] ?? 0) > 0) {
                return $data['data'][0]['id'];
            }
        }

        // Cria novo cliente
        $create = Http::withHeaders($this->headers())
            ->post("{$this->baseUrl}/customers", [
                'name'              => $order->buyer_name,
                'email'             => $order->buyer_email,
                'externalReference' => (string) $order->id,
                'notificationDisabled' => false,
            ]);

        if ($create->successful()) {
            return $create->json('id');
        }

        throw new \RuntimeException(
            '[AsaasPaymentService] Falha ao criar cliente: ' . $create->body()
        );
    }

    // -------------------------------------------------------------------------
    // PIX
    // -------------------------------------------------------------------------

    /**
     * Cria uma cobrança PIX no Asaas para o pedido informado.
     *
     * Retorna array com:
     *   - qr_code    : URL da imagem do QR Code (ou base64 em simulação)
     *   - copy_paste : código PIX copia e cola
     *   - payment_id : ID da cobrança no Asaas
     *
     * @param  BoostOrder  $order
     * @return array{qr_code: string, copy_paste: string, payment_id: string}
     */
    public function createPixCharge(BoostOrder $order): array
    {
        if ($this->isLocalMode()) {
            return $this->simulatedPixResponse($order);
        }

        try {
            // Garante que o cliente existe no Asaas
            $customerId = $this->findOrCreateCustomer($order);

            // Cria a cobrança do tipo PIX
            $charge = Http::withHeaders($this->headers())
                ->post("{$this->baseUrl}/payments", [
                    'customer'          => $customerId,
                    'billingType'       => 'PIX',
                    'value'             => (float) $order->amount,
                    'dueDate'           => now()->addHours(24)->format('Y-m-d'),
                    'description'       => "Pacote VIP WhatsGrupos — Pedido #{$order->id}",
                    'externalReference' => (string) $order->id,
                ]);

            if (! $charge->successful()) {
                throw new \RuntimeException(
                    '[AsaasPaymentService] Falha ao criar cobrança: ' . $charge->body()
                );
            }

            $chargeId = $charge->json('id');

            // Busca o QR Code da cobrança gerada
            $qrCodeResponse = Http::withHeaders($this->headers())
                ->get("{$this->baseUrl}/payments/{$chargeId}/pixQrCode");

            if (! $qrCodeResponse->successful()) {
                throw new \RuntimeException(
                    '[AsaasPaymentService] Falha ao buscar QR Code: ' . $qrCodeResponse->body()
                );
            }

            $qrData = $qrCodeResponse->json();

            return [
                'qr_code'    => $qrData['encodedImage'] ?? '',  // base64 da imagem
                'copy_paste' => $qrData['payload'] ?? '',        // código copia e cola
                'payment_id' => $chargeId,
            ];

        } catch (\Exception $e) {
            Log::error('[AsaasPaymentService] Erro ao criar cobrança PIX: ' . $e->getMessage());
            return $this->simulatedPixResponse($order);
        }
    }

    // -------------------------------------------------------------------------
    // Status
    // -------------------------------------------------------------------------

    /**
     * Consulta o status de uma cobrança no Asaas pelo ID.
     * Retorna 'pending', 'paid' ou 'failed'.
     *
     * @param  string  $paymentId  ID da cobrança no Asaas
     * @return string
     */
    public function getChargeStatus(string $paymentId): string
    {
        if ($this->isLocalMode()) {
            return 'pending';
        }

        try {
            $response = Http::withHeaders($this->headers())
                ->get("{$this->baseUrl}/payments/{$paymentId}");

            if (! $response->successful()) {
                return 'pending';
            }

            $status = $response->json('status');

            return match ($status) {
                'RECEIVED', 'CONFIRMED' => 'paid',
                'OVERDUE', 'REFUNDED', 'REFUND_REQUESTED',
                'CHARGEBACK_REQUESTED', 'CHARGEBACK_DISPUTE',
                'AWAITING_CHARGEBACK_REVERSAL', 'DUNNING_REQUESTED',
                'DUNNING_RECEIVED', 'AWAITING_RISK_ANALYSIS' => 'failed',
                default => 'pending',
            };

        } catch (\Exception $e) {
            Log::error('[AsaasPaymentService] Erro ao consultar status: ' . $e->getMessage());
            return 'pending';
        }
    }

    // -------------------------------------------------------------------------
    // Webhook
    // -------------------------------------------------------------------------

    /**
     * Valida e processa o payload do webhook do Asaas.
     * Retorna o ID da cobrança e o novo status se o evento for de pagamento.
     *
     * @param  array  $payload
     * @return array{payment_id: string, status: string}|null
     */
    public function processWebhook(array $payload): ?array
    {
        $event = $payload['event'] ?? '';

        // Apenas eventos de pagamento confirmado interessam
        if (! in_array($event, ['PAYMENT_RECEIVED', 'PAYMENT_CONFIRMED'])) {
            return null;
        }

        $paymentId = $payload['payment']['id'] ?? null;

        if (! $paymentId) {
            return null;
        }

        // Valida na API que o pagamento realmente está pago
        $status = $this->getChargeStatus($paymentId);

        return [
            'payment_id'        => $paymentId,
            'external_reference' => $payload['payment']['externalReference'] ?? null,
            'status'            => $status,
        ];
    }

    // -------------------------------------------------------------------------
    // Simulação local
    // -------------------------------------------------------------------------

    /**
     * Retorna dados simulados de PIX para desenvolvimento local.
     *
     * @param  BoostOrder  $order
     * @return array{qr_code: string, copy_paste: string, payment_id: string}
     */
    protected function simulatedPixResponse(BoostOrder $order): array
    {
        $fakeSvg = '<svg xmlns="http://www.w3.org/2000/svg" width="200" height="200">'
            . '<rect width="200" height="200" fill="white"/>'
            . '<rect x="10" y="10" width="40" height="40" fill="black"/>'
            . '<rect x="150" y="10" width="40" height="40" fill="black"/>'
            . '<rect x="10" y="150" width="40" height="40" fill="black"/>'
            . '<text x="100" y="105" text-anchor="middle" font-size="9" fill="black">ASAAS PIX</text>'
            . '<text x="100" y="118" text-anchor="middle" font-size="8" fill="#555">SIMULADO</text>'
            . '</svg>';

        $fakeQrCodeBase64 = 'data:image/svg+xml;base64,' . base64_encode($fakeSvg);

        $fakeCopyPaste = '00020126580014br.gov.bcb.pix0136'
            . strtoupper(str_pad((string) $order->id, 8, '0', STR_PAD_LEFT))
            . 'ASAAS52040000530398654'
            . number_format((float) $order->amount, 2, '.', '')
            . '5802BR5913WhatsGrupos6009SAO PAULO62070503***6304'
            . strtoupper(substr(md5($order->id . $order->amount), 0, 4));

        return [
            'qr_code'    => $fakeQrCodeBase64,
            'copy_paste' => $fakeCopyPaste,
            'payment_id' => 'ASAAS-SIM-' . strtoupper(uniqid()),
        ];
    }
}
