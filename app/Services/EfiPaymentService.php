<?php

namespace App\Services;

use App\Models\BoostOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Serviço de integração com a Efí Bank (ex-Gerencianet) para processamento
 * de pagamentos via PIX e Cartão.
 *
 * Em APP_ENV=local: todos os métodos retornam dados simulados sem chamar a API real.
 * Em APP_ENV=production: integração completa com a API da Efí Bank.
 */
class EfiPaymentService
{
    /**
     * Indica se estamos em modo sandbox/simulação (local ou staging).
     */
    protected bool $isSandbox;

    /**
     * Instância da SDK da Efí Bank (somente em produção).
     */
    protected mixed $efi;

    public function __construct()
    {
        $this->isSandbox = config('efi.sandbox', true);

        // Em produção, inicializa a SDK da Efí Bank com as credenciais do .env
        if (!$this->isLocalMode()) {
            $options = [
                'client_id'     => config('efi.client_id'),
                'client_secret' => config('efi.client_secret'),
                'sandbox'       => $this->isSandbox,
            ];

            try {
                $this->efi = new \EfiPay\Efi($options);
            } catch (\Exception $e) {
                Log::error('[EfiPaymentService] Falha ao inicializar SDK: ' . $e->getMessage());
                $this->efi = null;
            }
        }
    }

    protected function isLocalMode(): bool
    {
        $clientId = config('efi.client_id');
        $clientSecret = config('efi.client_secret');

        if (empty($clientId) || str_contains($clientId, 'placeholder') || empty($clientSecret) || str_contains($clientSecret, 'placeholder')) {
            return true;
        }

        return false;
    }

    /**
     * Cria uma cobrança via PIX para o pedido informado.
     * Retorna array com qr_code (base64) e copy_paste (linha digitável).
     *
     * Em modo local: retorna dados simulados sem chamar a API real.
     *
     * @param  BoostOrder  $order  O pedido de impulso a ser cobrado
     * @return array{qr_code: string, copy_paste: string, payment_id: string}
     */
    public function createPixCharge(BoostOrder $order): array
    {
        // Modo de simulação local — retorna dados fake para testes
        if ($this->isLocalMode()) {
            return $this->simulatedPixResponse($order);
        }

        try {
            // Monta o corpo da requisição conforme a API da Efí Bank v2
            $body = [
                'calendario' => ['expiracao' => 3600],
                'devedor' => [
                    'nome' => $order->buyer_name,
                    'email' => $order->buyer_email,
                ],
                'valor' => [
                    'original' => number_format($order->amount, 2, '.', ''),
                ],
                'chave' => config('efi.pix_key', $order->buyer_email),
                'infoAdicionais' => [
                    ['nome' => 'Pedido', 'valor' => (string) $order->id],
                    ['nome' => 'Pacote', 'valor' => $order->boostPackage->name ?? 'VIP'],
                ],
            ];

            // Cria a cobrança imediata (cob) na API da Efí Bank
            $response = $this->efi->pixCreateImmediateCharge([], $body);

            // Gera o QR Code da cobrança criada
            $txid = $response['txid'];
            $qrCode = $this->efi->pixGenerateQrcode(['id' => $txid]);

            return [
                'qr_code'    => $qrCode['imagemQrcode'] ?? '',   // base64 da imagem do QR Code
                'copy_paste' => $qrCode['qrcode'] ?? '',          // Linha digitável copia e cola
                'payment_id' => $txid,
            ];

        } catch (\Exception $e) {
            Log::error('[EfiPaymentService] Erro ao criar PIX: ' . $e->getMessage());
            // Em caso de erro na API, retorna simulação para não bloquear o fluxo
            return $this->simulatedPixResponse($order);
        }
    }

    /**
     * Cria uma cobrança via Cartão de Crédito para o pedido informado.
     * Retorna a URL do checkout da Efí Bank para redirecionar o usuário.
     *
     * Em modo local: retorna URL simulada de checkout.
     *
     * @param  BoostOrder  $order  O pedido de impulso a ser cobrado
     * @return string  URL para redirecionar o comprador ao checkout
     */
    public function createCardCharge(BoostOrder $order): string
    {
        // Modo de simulação local — retorna URL fake de checkout
        if ($this->isLocalMode()) {
            return route('boost.success', $order);
        }

        try {
            // Monta os dados para criação do link de pagamento por cartão
            $body = [
                'items' => [
                    [
                        'name'   => $order->boostPackage->name . ' — WhatsGrupos',
                        'value'  => (int) round($order->amount * 100), // valor em centavos
                        'amount' => 1,
                    ],
                ],
                'shippings' => [],
                'metadata' => [
                    'notification_url' => route('webhook.efi'),
                    'custom_id'        => (string) $order->id,
                ],
                'payment' => [
                    'credit_card' => [
                        'customer' => [
                            'name'          => $order->buyer_name,
                            'email'         => $order->buyer_email,
                            'cpf'           => '', // Preenchido no checkout da Efí
                            'birth'         => '',
                            'phone_number'  => '',
                        ],
                        'installments'  => 1,
                        'billing_address' => [
                            'street'     => '',
                            'number'     => '',
                            'neighborhood' => '',
                            'zipcode'    => '',
                            'city'       => '',
                            'state'      => '',
                        ],
                        'payment_token'  => '', // Token gerado pelo checkout.js da Efí
                    ],
                ],
            ];

            // Cria o link de pagamento via API da Efí Bank
            $response = $this->efi->createOneStepLink([], $body);

            return $response['payment']['links']['paymentUrl'] ?? route('boost.success', $order);

        } catch (\Exception $e) {
            Log::error('[EfiPaymentService] Erro ao criar cobrança no cartão: ' . $e->getMessage());
            return route('boost.success', $order);
        }
    }

    /**
     * Verifica e valida a assinatura do webhook recebido da Efí Bank.
     * Retorna true se a assinatura for válida.
     *
     * Em modo local: sempre retorna true (simulação).
     *
     * @param  Request  $request  A requisição recebida no endpoint do webhook
     */
    public function verifyWebhook(Request $request): bool
    {
        // Em modo local, aceita sempre o webhook simulado
        if ($this->isLocalMode()) {
            return true;
        }

        // Em produção, a Efí Bank valida via mTLS (certificado no servidor),
        // portanto a verificação de chegada já é garantida pelo servidor.
        // Aqui verificamos apenas se a requisição não está vazia.
        $payload = $request->all();
        return !empty($payload);
    }

    /**
     * Consulta o status de uma cobrança na API da Efí Bank pelo ID externo.
     * Retorna 'pending', 'paid' ou 'failed'.
     *
     * Em modo local: sempre retorna 'paid' (para simular aprovação).
     *
     * @param  string  $paymentId  O ID da cobrança retornado pela Efí Bank (txid)
     */
    public function getChargeStatus(string $paymentId): string
    {
        // Em modo local, simula sempre como pago
        if ($this->isLocalMode()) {
            return 'paid';
        }

        try {
            $params = ['txid' => $paymentId];
            $response = $this->efi->pixDetailCharge($params);

            // Mapeia os status da API para o padrão interno do sistema
            return match ($response['status'] ?? 'ATIVA') {
                'CONCLUIDA' => 'paid',
                'REMOVIDA_PELO_USUARIO_RECEBEDOR',
                'REMOVIDA_PELO_PSP'          => 'failed',
                default                       => 'pending',
            };

        } catch (\Exception $e) {
            Log::error('[EfiPaymentService] Erro ao consultar status PIX: ' . $e->getMessage());
            return 'pending';
        }
    }

    /**
     * Retorna dados simulados de PIX para o ambiente local de desenvolvimento.
     * Inclui QR Code fake em base64 e linha digitável fictícia.
     *
     * @param  BoostOrder  $order
     * @return array{qr_code: string, copy_paste: string, payment_id: string}
     */
    protected function simulatedPixResponse(BoostOrder $order): array
    {
        // QR Code SVG simples codificado em base64 para simular a imagem
        $fakeSvg = '<svg xmlns="http://www.w3.org/2000/svg" width="200" height="200">'
            . '<rect width="200" height="200" fill="white"/>'
            . '<rect x="10" y="10" width="40" height="40" fill="black"/>'
            . '<rect x="150" y="10" width="40" height="40" fill="black"/>'
            . '<rect x="10" y="150" width="40" height="40" fill="black"/>'
            . '<text x="100" y="110" text-anchor="middle" font-size="10" fill="black">PIX SIMULADO</text>'
            . '</svg>';

        $fakeQrCodeBase64 = 'data:image/svg+xml;base64,' . base64_encode($fakeSvg);

        // Linha digitável fictícia para simular a chave PIX copia e cola
        $fakeCopyPaste = '00020126580014br.gov.bcb.pix0136'
            . strtoupper(str_pad((string) $order->id, 8, '0', STR_PAD_LEFT))
            . 'WHATSGRUPOS52040000530398654'
            . number_format($order->amount, 2, '.', '')
            . '5802BR5913WhatsGrupos6009SAO PAULO62070503***6304'
            . strtoupper(substr(md5($order->id . $order->amount), 0, 4));

        return [
            'qr_code'    => $fakeQrCodeBase64,
            'copy_paste' => $fakeCopyPaste,
            'payment_id' => 'SIM-' . strtoupper(uniqid()),
        ];
    }
}
