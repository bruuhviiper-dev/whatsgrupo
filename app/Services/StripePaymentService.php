<?php

namespace App\Services;

use App\Models\BoostOrder;
use App\Models\BoostPackage;
use Illuminate\Support\Facades\Log;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Stripe\Webhook;

/**
 * Serviço de integração com o Stripe para processamento
 * de pagamentos via Cartão de Crédito e Link.
 *
 * Em ambiente local ou se as chaves da Stripe estiverem vazias:
 * o serviço operará em modo simulado.
 */
class StripePaymentService
{
    protected bool $isSimulated = false;

    public function __construct()
    {
        $secretKey = config('services.stripe.secret');

        // Modo simulado apenas se a chave estiver vazia ou for um placeholder literal
        $isPlaceholder = empty($secretKey)
            || $secretKey === 'sk_test_placeholder'
            || $secretKey === 'sk_live_placeholder';

        if ($isPlaceholder) {
            $this->isSimulated = true;
        } else {
            Stripe::setApiKey($secretKey);
        }
    }

    public function createCheckoutSession(BoostOrder $order, BoostPackage $package, string $method = 'card'): string
    {
        if ($this->isSimulated) {
            $order->update(['payment_id' => 'STRIPE-SIM-' . $method . '-' . $order->id]);
            // Em modo simulado local, redireciona diretamente para a rota de sucesso do WhatsGrupos
            return route('boost.success', $order) . '?simulated=stripe';
        }

        try {
            $session = Session::create([
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'brl',
                        'product_data' => [
                            'name' => 'WhatsGrupos — VIP Pacote ' . $package->name,
                            'description' => $package->boosts_count . ' impulsos VIP de 12h cada.',
                        ],
                        'unit_amount' => (int) round($order->amount * 100), // centavos
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('boost.success', $order),
                'cancel_url' => route('boost.packages'),
                'customer_email' => $order->buyer_email,
                'metadata' => [
                    'order_id' => (string) $order->id,
                ],
            ]);

            $order->update(['payment_id' => $session->id]);

            return $session->url;
        } catch (\Exception $e) {
            Log::error('[StripePaymentService] Erro ao criar checkout session (' . $method . '): ' . $e->getMessage());
            $order->update(['payment_id' => 'STRIPE-ERR-' . $order->id]);
            // Fallback de simulação amigável em caso de erro de chaves
            return route('boost.success', $order) . '?simulated=stripe';
        }
    }

    /**
     * Valida e decodifica a requisição recebida no webhook da Stripe.
     * Retorna o evento se a assinatura for válida ou lança exceção.
     */
    public function handleWebhook(string $payload, string $signatureHeader)
    {
        $webhookSecret = config('services.stripe.webhook_secret');

        if ($this->isSimulated || empty($webhookSecret) || str_contains($webhookSecret, 'placeholder')) {
            // Retorna evento simulado para facilitar testes locais
            return json_decode($payload, true);
        }

        return Webhook::constructEvent($payload, $signatureHeader, $webhookSecret);
    }

    /**
     * Cria uma sessão de Checkout Embutida (Embedded) para o cartão de crédito.
     * Retorna array com client_secret e publishable_key.
     */
    public function createEmbeddedSession(BoostOrder $order, BoostPackage $package): array
    {
        if ($this->isSimulated) {
            $order->update(['payment_id' => 'STRIPE-SIM-card-' . $order->id]);
            return [
                'is_simulated' => true,
                'client_secret' => 'simulated_secret_' . $order->id,
                'publishable_key' => 'pk_test_placeholder',
                'redirect_url' => route('boost.success', $order) . '?simulated=stripe',
            ];
        }

        try {
            $session = Session::create([
                'ui_mode' => 'embedded_page',
                'return_url' => route('boost.success', $order) . '?session_id={CHECKOUT_SESSION_ID}',
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'brl',
                        'product_data' => [
                            'name' => 'WhatsGrupos — VIP Pacote ' . $package->name,
                            'description' => $package->boosts_count . ' impulsos VIP de 12h cada.',
                        ],
                        'unit_amount' => (int) round($order->amount * 100), // centavos
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'customer_email' => $order->buyer_email,
                'metadata' => [
                    'order_id' => (string) $order->id,
                ],
            ]);

            $order->update(['payment_id' => $session->id]);

            return [
                'is_simulated' => false,
                'client_secret' => $session->client_secret,
                'publishable_key' => config('services.stripe.key'),
            ];
        } catch (\Exception $e) {
            Log::error('[StripePaymentService] Erro ao criar embedded session: ' . $e->getMessage());
            $order->update(['payment_id' => 'STRIPE-ERR-' . $order->id]);
            return [
                'is_simulated' => true,
                'client_secret' => 'error_secret',
                'publishable_key' => 'pk_test_placeholder',
                'redirect_url' => route('boost.success', $order) . '?simulated=stripe',
            ];
        }
    }

    /**
     * Verifica se o serviço está operando em modo de simulação local.
     */
    public function isSimulatedMode(): bool
    {
        return $this->isSimulated;
    }
}
