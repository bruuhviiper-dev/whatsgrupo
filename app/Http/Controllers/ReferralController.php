<?php

namespace App\Http\Controllers;

use App\Services\ReferralService;
use Illuminate\Http\Request;

/**
 * Controller responsável por interceptar e processar os cliques do link de indicação de grupos.
 */
class ReferralController extends Controller
{
    private ReferralService $referralService;

    public function __construct(ReferralService $referralService)
    {
        $this->referralService = $referralService;
    }

    /**
     * Redireciona o usuário vindo de um link de indicação para o detalhe do grupo correspondente.
     * Salva o código de indicação na sessão para futuras conversões de entrada.
     */
    public function redirect(Request $request, string $code)
    {
        // Registra o clique e busca a assinatura do referral
        $referral = $this->referralService->registerClick($code, $request);

        if (!$referral || !$referral->group) {
            return redirect()->route('home')
                ->with('error', 'O link de convite acessado é inválido ou expirou.');
        }

        // Salva o código de indicação na sessão do usuário
        $request->session()->put('active_referral_code', $referral->code);

        // Redireciona para o detalhe do grupo de destino
        return redirect()->route('group.show', $referral->group->slug)
            ->with('success', "👋 Você foi convidado para conhecer este grupo! Clique no botão abaixo para entrar.");
    }
}
