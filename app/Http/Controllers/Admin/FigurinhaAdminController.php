<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Figurinha;
use App\Repositories\FigurinhaRepository;
use App\Services\FigurinhaService;
use App\Enums\FigurinhaStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FigurinhaAdminController extends Controller
{
    public function __construct(
        protected FigurinhaRepository $repository,
        protected FigurinhaService $service
    ) {}

    public function index(Request $request)
    {
        $query = Figurinha::latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('categoria')) {
            $query->where('categoria', $request->categoria);
        }

        if ($request->filled('busca')) {
            $query->where('titulo', 'like', '%' . $request->busca . '%');
        }

        $figurinhas = $query->paginate(20)->withQueryString();
        $pendentes  = Figurinha::pendentes()->count();

        return view('admin.figurinhas.index', compact('figurinhas', 'pendentes'));
    }

    public function aprovar(Figurinha $figurinha)
    {
        $this->service->aprovar($figurinha);

        return back()->with('success', "Figurinha \"{$figurinha->titulo}\" aprovada com sucesso!");
    }

    public function rejeitar(Request $request, Figurinha $figurinha)
    {
        $request->validate([
            'motivo' => ['required', 'string', 'max:500'],
        ]);

        $this->service->rejeitar($figurinha, $request->motivo);

        return back()->with('success', "Figurinha \"{$figurinha->titulo}\" rejeitada.");
    }

    public function destroy(Figurinha $figurinha)
    {
        // Remove o arquivo do disco antes de excluir
        if ($figurinha->arquivo_path && Storage::disk('public')->exists($figurinha->arquivo_path)) {
            Storage::disk('public')->delete($figurinha->arquivo_path);
        }

        $figurinha->forceDelete();

        return back()->with('success', 'Figurinha excluída permanentemente.');
    }
}
