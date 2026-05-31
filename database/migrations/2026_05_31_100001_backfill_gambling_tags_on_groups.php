<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Reprocessa todos os grupos existentes e aplica is_gambling=1
     * para aqueles cujo nome ou descrição contenham palavras-chave de apostas.
     * Roda logo após a migration que criou a coluna.
     */
    public function up(): void
    {
        $keywords = config('prohibited_words.gambling', []);

        if (empty($keywords)) {
            return;
        }

        $groups = DB::table('groups')->select('id', 'name', 'description')->get();

        foreach ($groups as $group) {
            $text = mb_strtolower(($group->name ?? '') . ' ' . ($group->description ?? ''));

            foreach ($keywords as $keyword) {
                if (str_contains($text, mb_strtolower($keyword))) {
                    DB::table('groups')
                        ->where('id', $group->id)
                        ->update(['is_gambling' => 1]);
                    break;
                }
            }
        }
    }

    public function down(): void
    {
        // Não faz rollback — a remoção da coluna já cuida disso via migration anterior
    }
};
