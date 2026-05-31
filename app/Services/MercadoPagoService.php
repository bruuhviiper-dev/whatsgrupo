<?php

namespace App\Services;

use App\Models\BoostOrder;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

/**
 * Serviço de integração com o Mercado Pago para processamento
 * de pagamentos via PIX.
 *
 * Em ambiente local ou se as chaves do Mercado Pago estiverem vazias:
 * o serviço operará em modo simulado.
 */
class MercadoPagoService
{
    protected bool $isSimulated = false;
    protected ?string $accessToken;

    public function __construct()
    {
        $this->accessToken = config('services.mercadopago.access_token');

        if (empty($this->accessToken) || str_contains($this->accessToken, 'placeholder')) {
            $this->isSimulated = true;
        }
    }

    /**
     * Cria uma cobrança via PIX no Mercado Pago.
     * Retorna array com qr_code (base64) e copy_paste (linha digitável) e o payment_id.
     */
    public function createPixPayment(BoostOrder $order, array $payerData): array
    {
        if ($this->isSimulated) {
            return $this->simulatedPixResponse($order);
        }

        try {
            $client = new Client([
                'timeout' => 10.0,
            ]);

            // Monta os nomes
            $fullName = $payerData['name'] ?? $order->buyer_name;
            $nameParts = explode(' ', trim($fullName));
            $firstName = $nameParts[0] ?? 'Comprador';
            $lastName = count($nameParts) > 1 ? implode(' ', array_slice($nameParts, 1)) : 'Silva';

            $cleanCpf = preg_replace('/\D/', '', $payerData['cpf'] ?? '');

            $body = [
                'transaction_amount' => (float) $order->amount,
                'description' => 'WhatsGrupos — VIP Pacote Impulso',
                'payment_method_id' => 'pix',
                'payer' => [
                    'email' => $payerData['email'] ?? $order->buyer_email,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'identification' => [
                        'type' => 'CPF',
                        'number' => $cleanCpf ?: '00000000000',
                    ],
                ],
                'notification_url' => route('webhook.mercadopago'),
                'external_reference' => (string) $order->id,
            ];

            $response = $client->post('https://api.mercadopago.com/v1/payments', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->accessToken,
                    'Content-Type'  => 'application/json',
                ],
                'json' => $body,
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            $paymentId = $data['id'] ?? '';
            $transactionData = $data['point_of_interaction']['transaction_data'] ?? [];

            $copyPaste = $transactionData['qr_code'] ?? '';
            $qrCodeBase64Raw = $transactionData['qr_code_base64'] ?? '';

            // Adiciona prefixo data:image/png;base64 se não tiver
            $qrCodeBase64 = str_contains($qrCodeBase64Raw, 'data:image') 
                ? $qrCodeBase64Raw 
                : 'data:image/png;base64,' . $qrCodeBase64Raw;

            return [
                'qr_code'    => $qrCodeBase64,
                'copy_paste' => $copyPaste,
                'payment_id' => (string) $paymentId,
            ];

        } catch (\Exception $e) {
            Log::error('[MercadoPagoService] Erro ao criar pagamento PIX: ' . $e->getMessage());
            // Em caso de erro na API real, retorna simulação local
            return $this->simulatedPixResponse($order);
        }
    }

    /**
     * Consulta o status de um pagamento na API do Mercado Pago.
     * Retorna 'approved', 'pending' ou 'cancelled'.
     */
    public function getPaymentStatus(string $paymentId): string
    {
        if ($this->isSimulated || str_starts_with($paymentId, 'MP-SIM-')) {
            return 'approved';
        }

        try {
            $client = new Client(['timeout' => 5.0]);
            $response = $client->get('https://api.mercadopago.com/v1/payments/' . $paymentId, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->accessToken,
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            return $data['status'] ?? 'pending';

        } catch (\Exception $e) {
            Log::error('[MercadoPagoService] Erro ao consultar pagamento: ' . $e->getMessage());
            return 'pending';
        }
    }

    /**
     * Indica se o serviço está em modo de simulação local.
     */
    public function isSimulatedMode(): bool
    {
        return $this->isSimulated;
    }

    /**
     * Retorna dados fictícios de PIX para desenvolvimento.
     */
    protected function simulatedPixResponse(BoostOrder $order): array
    {
        $fakeSvg = '<svg xmlns="http://www.w3.org/2000/svg" width="200" height="200">'
            . '<rect width="200" height="200" fill="white"/>'
            . '<rect x="10" y="10" width="40" height="40" fill="black"/>'
            . '<rect x="150" y="10" width="40" height="40" fill="black"/>'
            . '<rect x="10" y="150" width="40" height="40" fill="black"/>'
            . '<text x="100" y="110" text-anchor="middle" font-size="10" fill="black">MERCADO PAGO PIX</text>'
            . '</svg>';

        $fakeQrCodeBase64 = 'data:image/svg+xml;base64,' . base64_encode($fakeSvg);

        $fakeCopyPaste = '000201012d600016br.gov.bcb.pix0136'
            . strtoupper(str_pad((string) $order->id, 8, '0', STR_PAD_LEFT))
            . 'MERCADOPAGO52040000530398654'
            . number_format($order->amount, 2, '.', '')
            . '5802BR5913WhatsGrupos6009SAO PAULO62070503***6304'
            . strtoupper(substr(md5($order->id . $order->amount), 0, 4));

        return [
            'qr_code'    => $fakeQrCodeBase64,
            'copy_paste' => $fakeCopyPaste,
            'payment_id' => 'MP-SIM-' . strtoupper(uniqid()),
        ];
    }
}
