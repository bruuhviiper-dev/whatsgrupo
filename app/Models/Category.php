<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    // Campos que podem ser preenchidos em massa
    protected $fillable = [
        'name',
        'slug',
        'icon',
        'order',
    ];

    /**
     * Relacionamento com os Grupos (Uma categoria possui muitos grupos).
     */
    public function groups(): HasMany
    {
        return $this->hasMany(Group::class);
    }

    /**
     * Relacionamento com patrocínio ativo de categoria.
     */
    public function sponsoredCategory()
    {
        return $this->hasOne(SponsoredCategory::class);
    }

    /**
     * Escopo para ordenar as categorias por ordem de exibição e depois por nome.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc')->orderBy('name', 'asc');
    }

    /**
     * Retorna o ícone do Heroicons correspondente ao slug da categoria.
     */
    public static function getHeroiconBySlug(?string $slug): string
    {
        $map = [
            'amizade'           => 'heroicon-o-users',
            'amor-e-romance'    => 'heroicon-o-heart',
            'carros-e-motos'    => 'heroicon-o-truck',
            'cidades'           => 'heroicon-o-building-office-2',
            'compra-e-venda'    => 'heroicon-o-shopping-cart',
            'concursos'         => 'heroicon-o-academic-cap',
            'desenhos-e-animes' => 'heroicon-o-sparkles',
            'divulgacao'        => 'heroicon-o-megaphone',
            'educacao'          => 'heroicon-o-book-open',
            'emagrecimento'     => 'heroicon-o-bolt',
            'esportes'          => 'heroicon-o-trophy',
            'eventos'           => 'heroicon-o-calendar-days',
            'fas'               => 'heroicon-o-star',
            'figurinhas'        => 'heroicon-o-face-smile',
            'filmes-e-series'   => 'heroicon-o-film',
            'frases-e-mensagens'=> 'heroicon-o-chat-bubble-left-ellipsis',
            'futebol'           => 'heroicon-o-trophy',
            'games-e-jogos'     => 'heroicon-o-device-phone-mobile',
            'ganhar-dinheiro'   => 'heroicon-o-currency-dollar',
            'imobiliaria'       => 'heroicon-o-home',
            'investimentos'     => 'heroicon-o-presentation-chart-line',
            'links'             => 'heroicon-o-link',
            'memes-e-zoeira'    => 'heroicon-o-face-smile',
            'moda-e-beleza'     => 'heroicon-o-scissors',
            'musica'            => 'heroicon-o-musical-note',
            'namoro'            => 'heroicon-o-heart',
            'negocios'          => 'heroicon-o-briefcase',
            'noticias'          => 'heroicon-o-newspaper',
            'outros'            => 'heroicon-o-squares-2x2',
            'politica'          => 'heroicon-o-scale',
            'profissoes'        => 'heroicon-o-identification',
            'receitas'          => 'heroicon-o-cake',
            'redes-sociais'     => 'heroicon-o-globe-alt',
            'religiao'          => 'heroicon-o-shield-check',
            'shitpost'          => 'heroicon-o-trash',
            'tecnologia'        => 'heroicon-o-cpu-chip',
            'tv'                => 'heroicon-o-tv',
            'vagas-de-emprego'  => 'heroicon-o-briefcase',
            'viagem-e-turismo'  => 'heroicon-o-map',
            'videos'            => 'heroicon-o-video-camera',
        ];

        return $map[$slug] ?? 'heroicon-o-folder';
    }

    /**
     * Acessador dinâmico para obter o nome do componente Heroicon.
     */
    public function getHeroiconAttribute(): string
    {
        return self::getHeroiconBySlug($this->slug);
    }
}
