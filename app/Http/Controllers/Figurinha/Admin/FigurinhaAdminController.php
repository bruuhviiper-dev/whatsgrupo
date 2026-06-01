<?php

namespace App\Http\Controllers\Figurinha\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Figurinha;
use App\Repositories\FigurinhaRepository;
use App\Services\FigurinhaService;

class FigurinhaAdminController extends Controller
{
    public function __construct(
        protected FigurinhaRepository $repository,
        protected FigurinhaService $service
    ) {}

    public function index(Request $request)
    {
        $status = $request->query('status', 'pendente');
        
        $query = Figurinha::latest();
        
        if ($status === 'pendente') {
            $query->pendentes();
        } elseif ($status === 'aprovada') {
            $query->aprovadas();
        } elseif ($status === 'rejeitada') {
            $query->where('status', \App\Enums\FigurinhaStatus::Rejeitado);
        }

        $figurinhas = $query->paginate(20);

        return view('admin.figurinhas.index', compact('figurinhas', 'status'));
    }

    public function aprovar(Figurinha $figurinha)
    {
        $this->service->aprovar($figurinha);
        
        return back()->with('success', 'Figurinha aprovada com sucesso.');
    }

    public function rejeitar(Figurinha $figurinha, Request $request)
    {
        $request->validate(['motivo' => 'required|string|max:255']);
        
        $this->service->rejeitar($figurinha, $request->motivo);
        
        return back()->with('success', 'Figurinha rejeitada.');
    }

    public function destroy(Figurinha $figurinha)
    {
        $figurinha->delete();
        
        return back()->with('success', 'Figurinha apagada com sucesso.');
    }
}
