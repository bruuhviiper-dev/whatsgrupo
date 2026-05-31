<?php

namespace App\Services;

use App\Models\Group;
use Illuminate\Support\Facades\Log;

/**
 * Serviço responsável por verificar a saúde dos links de grupos aprovados,
 * desativando aqueles que se tornarem inativos ou mortos.
 */
class LinkCheckerService
{
    /**
     * Verifica e atualiza o status de todos os grupos aprovados em chunks de 50.
     * Retorna um relatório com a quantidade verificada e desativada.
     */
    public function check(): array
    {
        $report = [
            'checked'     => 0,
            'deactivated' => 0
        ];

        $pythonBin = env('PYTHON_BIN', 'python');
        $scriptPath = base_path('python-service/collector/link_checker.py');
        $cmd = escapeshellcmd("{$pythonBin} {$scriptPath}");

        $descriptorspec = [
            0 => ["pipe", "r"], // stdin
            1 => ["pipe", "w"], // stdout
            2 => ["pipe", "w"]  // stderr
        ];

        // Processa grupos aprovados em chunks de 50
        Group::approved()->chunk(50, function ($groups) use ($cmd, $descriptorspec, &$report) {
            $chunkData = [];
            foreach ($groups as $group) {
                $chunkData[] = [
                    'id'   => $group->id,
                    'link' => $group->whatsapp_link
                ];
            }

            if (empty($chunkData)) {
                return;
            }

            $process = proc_open($cmd, $descriptorspec, $pipes);
            $stdout = '';
            $stderr = '';

            if (is_resource($process)) {
                fwrite($pipes[0], json_encode($chunkData));
                fclose($pipes[0]);

                $stdout = stream_get_contents($pipes[1]);
                fclose($pipes[1]);

                $stderr = stream_get_contents($pipes[2]);
                fclose($pipes[2]);

                $returnValue = proc_close($process);

                if ($returnValue !== 0) {
                    Log::error("[LinkCheckerService] Erro no verificador de links do Python. Erro: {$stderr}");
                    return;
                }
            } else {
                Log::error("[LinkCheckerService] Não foi possível iniciar o processo de link checker do Python.");
                return;
            }

            $results = json_decode($stdout, true);

            if (!is_array($results)) {
                Log::warning("[LinkCheckerService] Retorno inválido do link checker.");
                return;
            }

            foreach ($results as $result) {
                $groupId = $result['id'] ?? null;
                $isActive = $result['is_active'] ?? true;

                if (!$groupId) {
                    continue;
                }

                $report['checked']++;

                // Se o link for identificado como inativo (morto)
                if (!$isActive) {
                    $group = Group::find($groupId);
                    if ($group) {
                        $group->update([
                            'status' => 'rejected', // Rejeita/arquiva o grupo
                            'description' => $group->description . "\n\n[Inativado pelo Bot de Limpeza em " . now()->format('d/m/Y H:i') . ": Link expirado]"
                        ]);
                        $report['deactivated']++;
                    }
                }
            }
        });

        return $report;
    }
}
