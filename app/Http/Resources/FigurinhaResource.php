<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FigurinhaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'titulo' => $this->titulo,
            'slug' => $this->slug,
            'url_arquivo' => $this->url_arquivo,
            'categoria' => $this->categoria,
            'tags' => $this->tags,
            'downloads' => $this->downloads,
            'visualizacoes' => $this->visualizacoes,
            'status' => $this->status->value,
            'criado_em' => $this->created_at->toIso8601String(),
        ];
    }
}
