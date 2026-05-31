<?php

namespace App\Jobs;

use App\Models\Group;
use App\Services\WebPushService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Exception;

/**
 * Job para disparar notificações Web Push em segundo plano quando um grupo é aprovado.
 */
class SendNewGroupPushJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private Group $group;

    /**
     * Cria uma nova instância de job.
     */
    public function __construct(Group $group)
    {
        $this->group = $group;
    }

    /**
     * Executa o Job.
     */
    public function handle(WebPushService $service): void
    {
        // Garante que o grupo está aprovado e que possui categoria
        if ($this->group->status !== 'approved' || !$this->group->category) {
            return;
        }

        $title = "🔔 Novo Grupo em {$this->group->category->name}!";
        $body = "Entre agora no grupo \"{$this->group->name}\" que acabou de ser postado!";
        $url = route('group.show', $this->group->slug);

        try {
            $service->sendToCategory($this->group->category_id, $title, $body, $url);
        } catch (Exception $e) {
            \Illuminate\Support\Facades\Log::error("[SendNewGroupPushJob] Falha ao enviar notificação push: " . $e->getMessage());
        }
    }
}
