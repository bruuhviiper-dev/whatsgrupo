<?php

namespace App\Console\Commands;

use App\Models\Group;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class ReprocessGamblingTags extends Command
{
    protected $signature   = 'groups:reprocess-gambling {--dry-run : Apenas lista, não salva}';
    protected $description = 'Reprocessa todos os grupos existentes e aplica/remove a tag is_gambling com base nas palavras-chave atuais.';

    public function handle(): int
    {
        if (! Schema::hasColumn('groups', 'is_gambling')) {
            $this->error('Coluna is_gambling não existe. Execute: php artisan migrate');
            return self::FAILURE;
        }

        $isDryRun = $this->option('dry-run');
        $groups   = Group::all(['id', 'name', 'description', 'is_gambling']);
        $tagged   = 0;
        $untagged = 0;

        $this->info("Processando {$groups->count()} grupos...\n");

        foreach ($groups as $group) {
            $detected = Group::detectGambling($group->name, $group->description ?? '');

            if ($detected && ! $group->is_gambling) {
                $this->line("  🎲 [MARCAR] #{$group->id} — {$group->name}");
                if (! $isDryRun) {
                    $group->update(['is_gambling' => true]);
                }
                $tagged++;
            } elseif (! $detected && $group->is_gambling) {
                // Não remove tags inseridas manualmente — apenas reporta
                $this->line("  ⚠️  [MANUAL] #{$group->id} — {$group->name} (tag manual, não removida)");
            }
        }

        $this->newLine();

        if ($isDryRun) {
            $this->warn("Modo dry-run — nenhuma alteração salva. {$tagged} grupo(s) seriam tagueados.");
        } else {
            $this->info("✅ Concluído! {$tagged} grupo(s) tagueado(s) como gambling.");
        }

        return self::SUCCESS;
    }
}
