<?php

namespace App\Services;

use App\Models\Figurinha;
use App\Repositories\FigurinhaRepository;
use App\Http\Requests\StoreFigurinhaRequest;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FigurinhaService
{
    public function __construct(
        protected FigurinhaRepository $repository
    ) {}

    public function store(StoreFigurinhaRequest $request): Figurinha
    {
        $file = $request->file('arquivo');
        
        // Estrutura de pastas: figurinhas/ano/mes
        $folder = 'figurinhas/' . date('Y/m');
        
        // Nome único para o arquivo webp
        $filename = Str::uuid() . '.webp';
        $path = $folder . '/' . $filename;

        // Processamento com Intervention Image (V2)
        $img = Image::make($file);
        
        // Redimensionar para 512x512 mantendo a proporção (com fundo transparente se necessário)
        $img->resize(512, 512, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        
        // Criar um canvas 512x512 transparente e inserir a imagem redimensionada no centro
        $canvas = Image::canvas(512, 512);
        $canvas->insert($img, 'center');
        
        // Codificar em WebP com 90% de qualidade
        $webpData = (string) $canvas->encode('webp', 90);
        
        // Salvar no Storage Público
        Storage::disk('public')->put($path, $webpData);

        // Tags do JSON (explode por vírgula se for string, mas o Request já formata)
        $tags = $request->tags ?? [];

        // Trata tags se vier como string separada por vírgulas de alguns inputs
        if (is_string($tags)) {
            $tags = array_map('trim', explode(',', $tags));
        }

        // Preparar dados para o Repository
        $data = [
            'titulo' => $request->titulo,
            'arquivo_path' => $path,
            'arquivo_original' => $file->getClientOriginalName(),
            'categoria' => $request->categoria,
            'tags' => array_filter($tags),
            'user_id' => auth()->id(),
            'ip_envio' => $request->ip(),
        ];

        return $this->repository->create($data);
    }

    public function aprovar(Figurinha $figurinha): void
    {
        $this->repository->aprovar($figurinha);
    }

    public function rejeitar(Figurinha $figurinha, string $motivo): void
    {
        $this->repository->rejeitar($figurinha, $motivo);
    }

    public function incrementarDownload(Figurinha $figurinha): void
    {
        $figurinha->increment('downloads');
    }

    public function incrementarVisualizacao(Figurinha $figurinha): void
    {
        $figurinha->increment('visualizacoes');
    }
}
