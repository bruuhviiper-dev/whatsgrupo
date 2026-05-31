<?php

namespace App\Services;

use App\Models\Group;

/**
 * Serviço responsável por calcular e atualizar os scores de relevância de grupos.
 */
class GroupScoringService
{
    /**
     * Calcula o score de um grupo individual.
     */
    public function calculateScore(Group $group): float
    {
        // Peso 1: Cliques (engajamento real) — 40%
        // Máximo atingido com 100 cliques
        $clickScore = min($group->clicks / 100, 1) * 40;
        
        // Peso 2: Views (interesse geral) — 20%
        // Máximo atingido com 500 visualizações
        $viewScore = min($group->views / 500, 1) * 20;
        
        // Peso 3: Recência (idade do cadastro) — 30%
        // Grupo de hoje = 30, grupo com 365 dias ou mais = 0
        $daysSince = now()->diffInDays($group->created_at);
        $recencyScore = max(0, (365 - $daysSince) / 365) * 30;
        
        // Peso 4: Completude do perfil — 10%
        $completeness = 0;
        if ($group->image_path) {
            $completeness += 5;
        }
        if (strlen($group->description) > 100) {
            $completeness += 3;
        }
        if ($group->rules) {
            $completeness += 2;
        }
        
        return (float) round($clickScore + $viewScore + $recencyScore + $completeness, 4);
    }
    
    /**
     * Recalcula o score de todos os grupos aprovados no banco de dados.
     * Processa em blocos de 200 para alta performance de memória.
     */
    public function recalculateAll(): int
    {
        $count = 0;
        
        Group::approved()->chunk(200, function ($groups) use (&$count) {
            foreach ($groups as $group) {
                $group->update([
                    'score' => $this->calculateScore($group)
                ]);
                $count++;
            }
        });
        
        return $count;
    }
}
