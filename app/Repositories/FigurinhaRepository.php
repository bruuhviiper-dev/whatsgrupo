<?php

namespace App\Repositories;

use App\Models\Figurinha;
use App\Enums\FigurinhaStatus;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class FigurinhaRepository
{
    public function create(array $data): Figurinha
    {
        return Figurinha::create($data);
    }

    public function findBySlug(string $slug): Figurinha
    {
        return Figurinha::where('slug', $slug)->firstOrFail();
    }

    public function listarAprovadas(?string $categoria = null, ?string $busca = null): LengthAwarePaginator
    {
        $query = Figurinha::aprovadas()->latest('aprovado_em');

        if ($categoria) {
            $query->porCategoria($categoria);
        }

        if ($busca) {
            $query->where(function ($q) use ($busca) {
                $q->where('titulo', 'like', "%{$busca}%")
                  ->orWhereJsonContains('tags', $busca);
            });
        }

        return $query->paginate(20);
    }

    public function listarPendentes(): Collection
    {
        return Figurinha::pendentes()->latest()->get();
    }

    public function aprovar(Figurinha $figurinha): void
    {
        $figurinha->update([
            'status' => FigurinhaStatus::Aprovado,
            'aprovado_em' => now(),
        ]);
    }

    public function rejeitar(Figurinha $figurinha, string $motivo): void
    {
        $figurinha->update([
            'status' => FigurinhaStatus::Rejeitado,
            'motivo_rejeicao' => $motivo,
        ]);
    }
}
