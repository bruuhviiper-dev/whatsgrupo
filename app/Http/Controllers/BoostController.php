<?php

namespace App\Http\Controllers;

use App\Mail\BoostCodeMail;
use App\Models\BoostOrder;
use App\Models\BoostPackage;
use App\Models\Group;
use App\Services\EfiPaymentService;
use App\Services\StripePaymentService;
use App\Services\AsaasPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Controller responsável pelo fluxo de compra de pacotes VIP (Boost):
 * listagem de pacotes, checkout PIX/Cartão, polling de status, webhook e sucesso.
 */
class BoostController extends Controller
{
    public function __construct(
        protected EfiPaymentService $paymentService,
        protected StripePaymentService $stripeService,
        protected AsaasPaymentService $asaasService
    ) {
    }

    // -------------------------------------------------------------------------
    // Listagem de Pacotes VIP
    // -------------------------------------------------------------------------

    public function packages()
    {
        $packages = BoostPackage::where('is_active', true)
            ->orderBy('price', 'asc')
            ->get();

        // Contador de grupos impulsionados no mês atual para prova social
        $boostedThisMonth = Group::notExpiredVip()
            ->whereMonth('vip_expires_at', now()->month)
            ->count();

        return view('boost-packages', compact('packages', 'boostedThisMonth'));
    }

    // -------------------------------------------------------------------------
    // Página de Checkout do Pacote
    // -------------------------------------------------------------------------

    public function checkout(BoostPackage $package)
    {
        abort_if(!$package->is_active, 404);

        return view('checkout', compact('package'));
    }

    // -------------------------------------------------------------------------
    // Processamento do Checkout (cria pedido e aciona pagamento)
    // -------------------------------------------------------------------------

    public function processCheckout(Request $request, BoostPackage $package)
    {
        abort_if(!$package->is_active, 404);

        $validated = $request->validate([
            'buyer_name'       => 'required|string|min:3|max:150',
            'buyer_email'      => 'required|email|max:255',
            'payment_method'   => 'required|in:pix,card',
        ], [
            'buyer_name.required'     => 'Informe seu nome completo.',
            'buyer_email.required'    => 'Informe seu e-mail.',
            'payment_method.required' => 'Selecione a forma de pagamento.',
        ]);

        // Cria o pedido com status pendente
        $order = BoostOrder::create([
            'boost_package_id' => $package->id,
            'buyer_name'       => $validated['buyer_name'],
            'buyer_email'      => $validated['buyer_email'],
            'payment_method'   => $validated['payment_method'],
            'payment_status'   => 'pending',
            'boosts_total'     => $package->boosts_count,
            'boosts_used'      => 0,
            'amount'           => $package->price,
        ]);

        // Modo simulação: se o botão de simular pagamento foi clicado em ambiente local
        if (app()->environment('local') && $request->has('simulate_payment')) {
            return $this->simulatePaymentApproved($order);
        }

        // Se a Stripe estiver configurada com chaves reais (produção ou testes ativos):
        if (!$this->stripeService->isSimulatedMode()) {
            // Cria sessão de checkout na Stripe para o método selecionado (pix ou card)
            $checkoutUrl = $this->stripeService->createCheckoutSession($order, $package, $validated['payment_method']);
            return redirect()->away($checkoutUrl);
        }

        // Em modo de simulação local (sem chaves Stripe configuradas):
        if ($validated['payment_method'] === 'pix') {
            // Cria a cobrança PIX via Efí Bank simulada e salva dados do QR Code no pedido
            $pixData = $this->paymentService->createPixCharge($order);

            $order->update([
                'payment_id'    => $pixData['payment_id'],
                'pix_qr_code'   => $pixData['qr_code'],
                'pix_copy_paste' => $pixData['copy_paste'],
            ]);

            return view('checkout', [
                'package'   => $package,
                'order'     => $order,
                'pixData'   => $pixData,
                'showPix'   => true,
            ]);

        } else {
            // Cria sessão de checkout simulada no Stripe para cartão
            $checkoutUrl = $this->stripeService->createCheckoutSession($order, $package, 'card');

            return redirect()->away($checkoutUrl);
        }
    }

    // -------------------------------------------------------------------------
    // Polling de Status PIX (chamado via AJAX a cada 5 segundos)
    // -------------------------------------------------------------------------

    public function pixStatus(BoostOrder $order)
    {
        // Consulta o status atual na API da Efí (ou retorna 'paid' em modo local)
        $status = $this->paymentService->getChargeStatus($order->payment_id ?? '');

        if ($status === 'paid' && $order->payment_status !== 'paid') {
            $this->confirmPayment($order);
        }

        return response()->json([
            'status'      => $order->fresh()->payment_status,
            'redirect_url' => $order->payment_status === 'paid'
                ? route('boost.success', $order)
                : null,
        ]);
    }

    // -------------------------------------------------------------------------
    // Página de Sucesso após Pagamento
    // -------------------------------------------------------------------------

    public function success(BoostOrder $order)
    {
        return view('payment-success', compact('order'));
    }

    // -------------------------------------------------------------------------
    // Webhook da Efí Bank (notificação de pagamento em produção)
    // -------------------------------------------------------------------------

    public function webhook(Request $request)
    {
        // Valida a assinatura/origem do webhook
        if (!$this->paymentService->verifyWebhook($request)) {
            Log::warning('[BoostController] Webhook recebido com assinatura inválida.');
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $payload = $request->all();
        Log::info('[BoostController] Webhook recebido:', $payload);

        // Identifica o txid/pagamento na notificação da Efí Bank
        $txid = $payload['pix'][0]['txid'] ?? $payload['txid'] ?? null;

        if (!$txid) {
            return response()->json(['ok' => true]);
        }

        // Busca o pedido correspondente ao payment_id recebido
        $order = BoostOrder::where('payment_id', $txid)
            ->where('payment_status', 'pending')
            ->first();

        if ($order) {
            $this->confirmPayment($order);
        }

        return response()->json(['ok' => true]);
    }

    /**
     * Webhook da Stripe para notificação de pagamentos recebidos (checkout.session.completed).
     */
    public function webhookStripe(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature') ?? '';

        try {
            $event = $this->stripeService->handleWebhook($payload, $sigHeader);
        } catch (\UnexpectedValueException $e) {
            Log::warning('[BoostController] Webhook Stripe recebido com payload inválido: ' . $e->getMessage());
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            Log::warning('[BoostController] Webhook Stripe recebido com assinatura inválida: ' . $e->getMessage());
            return response()->json(['error' => 'Invalid signature'], 400);
        } catch (\Exception $e) {
            Log::warning('[BoostController] Erro desconhecido no Webhook Stripe: ' . $e->getMessage());
            return response()->json(['error' => 'Error'], 400);
        }

        // Tipo de evento que nos interessa
        $eventType = is_array($event) ? ($event['type'] ?? '') : ($event->type ?? '');

        if ($eventType === 'checkout.session.completed') {
            $session = is_array($event) ? ($event['data']['object'] ?? []) : ($event->data->object ?? null);
            $sessionId = is_array($session) ? ($session['id'] ?? '') : ($session->id ?? '');
            $orderId = is_array($session) ? ($session['metadata']['order_id'] ?? '') : ($session->metadata->order_id ?? '');

            Log::info("[BoostController] Stripe Session Completed. ID: {$sessionId}, OrderID: {$orderId}");

            // Busca o pedido correspondente ao payment_id (Session ID) ou ID de metadados
            $order = null;
            if ($orderId) {
                $order = BoostOrder::where('id', $orderId)
                    ->where('payment_status', 'pending')
                    ->first();
            }

            if (!$order && $sessionId) {
                $order = BoostOrder::where('payment_id', $sessionId)
                    ->where('payment_status', 'pending')
                    ->first();
            }

            if ($order) {
                $this->confirmPayment($order);
                Log::info("[BoostController] Pedido #{$order->id} confirmado via Stripe Webhook.");
            } else {
                Log::warning("[BoostController] Pedido correspondente não encontrado para Stripe Session ID: {$sessionId}, OrderID: {$orderId}");
            }
        }

        return response()->json(['ok' => true]);
    }

    // -------------------------------------------------------------------------
    // Confirmação de Pagamento (usado pelo webhook e pelo polling)
    // -------------------------------------------------------------------------

    protected function confirmPayment(BoostOrder $order): void
    {
        // Gera o código de boost único de 12 chars
        $boostCode = BoostOrder::generateCode();

        // Atualiza o pedido como pago e registra o código
        $order->update([
            'payment_status' => 'paid',
            'boost_code'     => $boostCode,
        ]);

        // Envia o e-mail com o código para o comprador
        try {
            Mail::to($order->buyer_email)->send(new BoostCodeMail($order->fresh()));
        } catch (\Exception $e) {
            Log::error('[BoostController] Falha ao enviar BoostCodeMail: ' . $e->getMessage());
        }
    }

    // -------------------------------------------------------------------------
    // Simulação de Pagamento Aprovado (só em APP_ENV=local)
    // -------------------------------------------------------------------------

    protected function simulatePaymentApproved(BoostOrder $order)
    {
        // Gera dados simulados de PIX para exibição na tela antes de confirmar
        $pixData = $this->paymentService->createPixCharge($order);

        $order->update([
            'payment_id'     => $pixData['payment_id'],
            'pix_qr_code'    => $pixData['qr_code'],
            'pix_copy_paste' => $pixData['copy_paste'],
        ]);

        // Confirma o pagamento imediatamente (simula aprovação instantânea)
        $this->confirmPayment($order);

        return redirect()->route('boost.success', $order->fresh())
            ->with('simulated', true);
    }

    /**
     * Cria sessão AJAX do Stripe Embedded Checkout na mesma tela.
     */
    public function checkoutStripeEmbedded(Request $request, BoostPackage $package)
    {
        abort_if(!$package->is_active, 404);

        $validated = $request->validate([
            'buyer_name'  => 'required|string|min:3|max:150',
            'buyer_email' => 'required|email|max:255',
        ]);

        $order = BoostOrder::create([
            'boost_package_id' => $package->id,
            'buyer_name'       => $validated['buyer_name'],
            'buyer_email'      => $validated['buyer_email'],
            'payment_method'   => 'card',
            'payment_status'   => 'pending',
            'boosts_total'     => $package->boosts_count,
            'boosts_used'      => 0,
            'amount'           => $package->price,
        ]);

        $sessionData = $this->stripeService->createEmbeddedSession($order, $package);

        return response()->json([
            'success'         => true,
            'is_simulated'    => $sessionData['is_simulated'],
            'client_secret'   => $sessionData['client_secret'],
            'publishable_key' => $sessionData['publishable_key'],
            'redirect_url'    => $sessionData['redirect_url'] ?? null,
            'order_id'        => $order->id,
        ]);
    }

    /**
     * Cria cobrança PIX no Asaas via AJAX na mesma tela.
     */
    public function checkoutAsaasPix(Request $request, BoostPackage $package)
    {
        abort_if(!$package->is_active, 404);

        $validated = $request->validate([
            'buyer_name'  => 'required|string|min:3|max:150',
            'buyer_email' => 'required|email|max:255',
        ]);

        $order = BoostOrder::create([
            'boost_package_id' => $package->id,
            'buyer_name'       => $validated['buyer_name'],
            'buyer_email'      => $validated['buyer_email'],
            'payment_method'   => 'pix',
            'payment_status'   => 'pending',
            'boosts_total'     => $package->boosts_count,
            'boosts_used'      => 0,
            'amount'           => $package->price,
        ]);

        // Cria a cobrança PIX via Asaas
        $pixData = $this->asaasService->createPixCharge($order);

        $order->update([
            'payment_id'     => $pixData['payment_id'],
            'pix_qr_code'    => $pixData['qr_code'],
            'pix_copy_paste' => $pixData['copy_paste'],
        ]);

        return response()->json([
            'success'      => true,
            'qr_code'      => $pixData['qr_code'],
            'copy_paste'   => $pixData['copy_paste'],
            'payment_id'   => $pixData['payment_id'],
            'order_id'     => $order->id,
            'redirect_url' => route('boost.success', $order),
        ]);
    }

    /**
     * Webhook do Asaas para notificação de pagamentos recebidos.
     */
    public function webhookAsaas(Request $request)
    {
        // Valida o token de autenticação do webhook (definido no painel do Asaas)
        $webhookToken = config('asaas.webhook_token', '');
        $receivedToken = $request->header('asaas-access-token', '');

        if (!empty($webhookToken) && $receivedToken !== $webhookToken) {
            Log::warning('[BoostController] Webhook Asaas com token inválido.', [
                'received' => $receivedToken,
            ]);
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $payload = $request->all();
        Log::info('[BoostController] Webhook Asaas recebido:', $payload);

        $result = $this->asaasService->processWebhook($payload);

        if (!$result) {
            // Evento irrelevante (ex: criação de cobrança, etc.)
            return response()->json(['ok' => true]);
        }

        if ($result['status'] !== 'paid') {
            return response()->json(['ok' => true]);
        }

        // Tenta encontrar o pedido pelo ID da cobrança no Asaas
        $order = BoostOrder::where('payment_id', $result['payment_id'])
            ->where('payment_status', 'pending')
            ->first();

        // Fallback: busca pela referência externa (ID do pedido no sistema)
        if (!$order && !empty($result['external_reference'])) {
            $order = BoostOrder::where('id', $result['external_reference'])
                ->where('payment_status', 'pending')
                ->first();
        }

        if ($order) {
            $this->confirmPayment($order);
            Log::info("[BoostController] Pedido #{$order->id} confirmado via Asaas Webhook.");
        } else {
            Log::warning('[BoostController] Pedido não encontrado para Asaas payment_id: ' . $result['payment_id']);
        }

        return response()->json(['ok' => true]);
    }
}
