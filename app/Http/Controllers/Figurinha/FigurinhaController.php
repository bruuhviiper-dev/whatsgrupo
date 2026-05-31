<?php

namespace App\Http\Controllers\Figurinha;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Repositories\FigurinhaRepository;
use App\Services\FigurinhaService;
use App\Http\Requests\StoreFigurinhaRequest;
use App\Enums\FigurinhaCategoria;
use Illuminate\Support\Facades\Storage;

class FigurinhaController extends Controller
{
    public function __construct(
        protected FigurinhaRepository $repository,
        protected FigurinhaService $service
    ) {}

    public function index(Request $request)
    {
        $categoria = $request->query('categoria');
        $busca = $request->query('busca');

        $figurinhas = $this->repository->listarAprovadas($categoria, $busca);
        $categorias = FigurinhaCategoria::cases();

        return view('figurinhas.index', compact('figurinhas', 'categorias', 'categoria', 'busca'));
    }

    public function create()
    {
        $categorias = FigurinhaCategoria::cases();
        return view('figurinhas.create', compact('categorias'));
    }

    public function store(StoreFigurinhaRequest $request)
    {
        $this->service->store($request);

        return redirect()->route('figurinhas.index')
            ->with('success', 'Sua figurinha foi enviada e está em análise. Obrigado pela contribuição!');
    }

    public function show(string $slug)
    {
        $figurinha = $this->repository->findBySlug($slug);
        
        $this->service->incrementarVisualizacao($figurinha);

        // Sugestões
        $relacionadas = $this->repository->listarAprovadas($figurinha->categoria->value)
            ->where('id', '!=', $figurinha->id)
            ->take(4);

        return view('figurinhas.show', compact('figurinha', 'relacionadas'));
    }

    public function download(string $slug)
    {
        $figurinha = $this->repository->findBySlug($slug);
        
        $this->service->incrementarDownload($figurinha);

        $path = Storage::disk('public')->path($figurinha->arquivo_path);

        if (!file_exists($path)) {
            abort(404);
        }

        return response()->download($path, $figurinha->slug . '.webp');
    }
}
