<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

use App\Enums\FigurinhaStatus;
use App\Enums\FigurinhaCategoria;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Figurinha extends Model
{
    use HasUlids, SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'tags' => 'array',
        'status' => FigurinhaStatus::class,
        'categoria' => FigurinhaCategoria::class, // <-- FALTAVA ESSA LINHA!
        'aprovado_em' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getUrlArquivoAttribute(): string
    {
        return Storage::disk('public')->url($this->arquivo_path);
    }

    public function scopeAprovadas(Builder $query): Builder
    {
        return $query->where('status', FigurinhaStatus::Aprovado);
    }

    public function scopePendentes(Builder $query): Builder
    {
        return $query->where('status', FigurinhaStatus::Pendente);
    }

    public function scopePorCategoria(Builder $query, string $categoria): Builder
    {
        return $query->where('categoria', $categoria);
    }

    // Formato moderno de Accessor (Mais seguro para o Laravel atual)
    protected function urlArquivo(): Attribute
    {
        return Attribute::make(
            get: fn() => Storage::disk('public')->url($this->arquivo_path),
        );
    }
}
