<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\BoostCodeMail;
use App\Models\BoostOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Controller para gerenciamento de pedidos de impulso no painel de administração.
 * Permite listagem com filtros e reenvio de e-mail com o código de boost.
 */
class OrderController extends Controller
{
    // -------------------------------------------------------------------------
    // Listagem de Pedidos com Filtros
    // -------------------------------------------------------------------------

    public function index(Request $request)
    {
        $query = BoostOrder::with('boostPackage')->orderBy('created_at', 'desc');

        // Filtro por status de pagamento
        if ($request->filled('status')) {
            $query->where('payment_status', $request->status);
        }

        // Filtro por data (formato YYYY-MM-DD)
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        // Busca por e-mail do comprador
        if ($request->filled('search')) {
            $query->where('buyer_email', 'like', '%' . $request->search . '%');
        }

        $orders = $query->paginate(20)->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    // -------------------------------------------------------------------------
    // Reenvio do E-mail com Código de Boost
    // -------------------------------------------------------------------------

    public function resendCode(BoostOrder $order)
    {
        // Só reenvia para pedidos que já foram pagos e têm código gerado
        if ($order->payment_status !== 'paid' || !$order->boost_code) {
            return back()->with('error', 'Este pedido não está pago ou não possui código gerado.');
        }

        try {
            Mail::to($order->buyer_email)->send(new BoostCodeMail($order));
            Log::info("[OrderController] Código reenviado para {$order->buyer_email} — Pedido #{$order->id}");
            return back()->with('success', "📧 Código reenviado para {$order->buyer_email}!");
        } catch (\Exception $e) {
            Log::error('[OrderController] Falha ao reenviar BoostCodeMail: ' . $e->getMessage());
            return back()->with('error', 'Falha ao enviar o e-mail. Verifique as configurações de SMTP.');
        }
    }
}
