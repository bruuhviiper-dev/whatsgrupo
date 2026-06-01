<?php

namespace App\Http\Controllers;

use App\Models\RaffleDraw;
use Illuminate\Http\Request;

class RaffleController extends Controller
{
    /**
     * Exibe a página principal da ferramenta.
     */
    public function index()
    {
        return view('tools.raffle-generator.index');
    }

    /**
     * Salva um sorteio no banco e retorna o UUID para compartilhar.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'              => 'nullable|string|max:200',
            'participants'       => 'required|array|min:2|max:500',
            'participants.*'     => 'required|string|max:200',
            'winners'            => 'required|array|min:1',
            'winners.*'          => 'required|string|max:200',
            'winner_count'       => 'required|integer|min:1',
            'mode'               => 'required|in:roulette,random',
            'total_participants' => 'required|integer|min:2',
        ]);

        $raffle = RaffleDraw::create([
            'title'              => $request->input('title') ?: 'Sorteio WhatsApp',
            'participants'       => $request->input('participants'),
            'winners'            => $request->input('winners'),
            'winner_count'       => $request->input('winner_count'),
            'mode'               => $request->input('mode'),
            'total_participants' => $request->input('total_participants'),
        ]);

        return response()->json([
            'uuid' => $raffle->uuid,
            'url'  => route('tools.raffle.show', $raffle->uuid),
        ]);
    }

    /**
     * Exibe a página pública do sorteio salvo.
     */
    public function show(string $uuid)
    {
        $raffle = RaffleDraw::where('uuid', $uuid)->firstOrFail();
        return view('tools.raffle-generator.show', compact('raffle'));
    }
    /**
     * Busca um sorteio pelo código curto ou UUID completo.
     */
    public function search(Request $request)
    {
        $code = trim($request->input('code'));
        if (!$code) {
            return back()->with('error', 'Por favor, insira um código de sorteio válido.');
        }

        $raffle = RaffleDraw::where('uuid', 'like', $code . '%')->first();

        if (!$raffle) {
            return back()->with('error', 'Sorteio não encontrado com o código fornecido.');
        }

        return redirect()->route('tools.raffle.show', $raffle->uuid);
    }

    /**
     * Envia o link do sorteio por e-mail.
     */
    public function sendEmail(Request $request, string $uuid)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $raffle = RaffleDraw::where('uuid', $uuid)->firstOrFail();
        $url = route('tools.raffle.show', $raffle->uuid);
        $code = strtoupper(explode('-', $raffle->uuid)[0]);

        try {
            \Illuminate\Support\Facades\Mail::raw(
                "Olá!\n\nAqui está o link oficial e o código do seu sorteio verificado no WhatsGrupos.\n\nCódigo de Verificação: {$code}\nLink Oficial: {$url}\n\nObrigado por usar nossa ferramenta!",
                function ($message) use ($request) {
                    $message->to($request->email)
                            ->subject('Link Oficial do seu Sorteio - WhatsGrupos');
                }
            );
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
