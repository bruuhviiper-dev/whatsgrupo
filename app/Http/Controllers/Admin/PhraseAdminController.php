<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StatusPhrase;
use Illuminate\Http\Request;

class PhraseAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = StatusPhrase::latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('categoria')) {
            $query->where('category', $request->categoria);
        }

        if ($request->filled('busca')) {
            $query->where(function ($q) use ($request) {
                $q->where('phrase', 'like', '%' . $request->busca . '%')
                  ->orWhere('author', 'like', '%' . $request->busca . '%');
            });
        }

        $phrases  = $query->paginate(25)->withQueryString();
        $pendentes = StatusPhrase::pendentes()->count();

        return view('admin.phrases.index', compact('phrases', 'pendentes'));
    }

    public function aprovar(StatusPhrase $phrase)
    {
        $phrase->update(['status' => 'aprovado', 'motivo_rejeicao' => null]);

        return back()->with('success', 'Frase aprovada com sucesso!');
    }

    public function aprovarTodas()
    {
        $total = StatusPhrase::pendentes()->count();

        if ($total === 0) {
            return back()->with('success', 'Nenhuma frase pendente para aprovar.');
        }

        StatusPhrase::pendentes()->update(['status' => 'aprovado', 'motivo_rejeicao' => null]);

        return back()->with('success', "{$total} frase(s) pendente(s) aprovada(s) com sucesso!");
    }

    public function rejeitar(Request $request, StatusPhrase $phrase)
    {
        $request->validate([
            'motivo' => ['required', 'string', 'max:500'],
        ]);

        $phrase->update(['status' => 'rejeitado', 'motivo_rejeicao' => $request->motivo]);

        return back()->with('success', 'Frase rejeitada.');
    }

    public function destroy(StatusPhrase $phrase)
    {
        $phrase->delete();

        return back()->with('success', 'Frase excluída permanentemente.');
    }
}
