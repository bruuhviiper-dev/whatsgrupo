<?php

namespace App\Http\Controllers;

use App\Models\PushSubscription;
use Illuminate\Http\Request;

/**
 * Controller responsável por receber as inscrições e preferências de Web Push.
 */
class PushController extends Controller
{
    /**
     * Registra ou atualiza a inscrição Web Push do navegador do usuário.
     */
    public function subscribe(Request $request)
    {
        $request->validate([
            'endpoint'     => 'required|string',
            'public_key'   => 'required|string',
            'auth_token'   => 'required|string',
            'category_ids' => 'nullable|array',
        ]);

        $subscription = PushSubscription::updateOrCreate(
            ['endpoint' => $request->endpoint],
            [
                'public_key'   => $request->public_key,
                'auth_token'   => $request->auth_token,
                'category_ids' => $request->category_ids ?? [], // Array vazio por padrão
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Inscrição de notificações Web Push registrada com sucesso!',
            'data'    => $subscription
        ]);
    }

    /**
     * Remove a inscrição de Web Push baseado no endpoint do navegador.
     */
    public function unsubscribe(Request $request)
    {
        $request->validate([
            'endpoint' => 'required|string',
        ]);

        PushSubscription::where('endpoint', $request->endpoint)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Assinatura de notificações cancelada com sucesso.'
        ]);
    }

    /**
     * Atualiza as categorias de preferência de notificações do usuário.
     */
    public function preferences(Request $request)
    {
        $request->validate([
            'endpoint'     => 'required|string',
            'category_ids' => 'required|array',
        ]);

        $subscription = PushSubscription::where('endpoint', $request->endpoint)->first();

        if ($subscription) {
            $subscription->update([
                'category_ids' => $request->category_ids
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Preferências de categorias salvas com sucesso!',
                'data'    => $subscription
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Inscrição de push não encontrada.'
        ], 404);
    }
}
