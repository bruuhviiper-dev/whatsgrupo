<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

/**
 * Analisa imagens em busca de conteúdo adulto/pornográfico via nudenet (Python).
 *
 * Funciona para URLs remotas (pps.whatsapp.net) e para paths locais de storage.
 * Fail-open: se o script Python não estiver disponível ou ocorrer erro, a imagem
 * é considerada segura — nunca bloqueia o cadastro por falha técnica da análise.
 */
class ImageCheckerService
{
    public function __construct(
        private readonly string $pythonBin  = '',
        private readonly string $scriptPath = '',
    ) {}

    private function getBin(): string
    {
        if ($this->pythonBin) return $this->pythonBin;
        return config('app.python_bin') ?: (PHP_OS_FAMILY === 'Windows' ? 'python' : 'python3');
    }

    private function getScript(): string
    {
        if ($this->scriptPath) return $this->scriptPath;
        return base_path('python-service/image_checker.py');
    }

    /**
     * Analisa uma imagem.
     *
     * @param  string  $imageInput  URL http/https ou path absoluto do arquivo
     * @return array{safe: bool, score: float, labels: string[], error: string|null}
     */
    public function check(string $imageInput): array
    {
        $safe = ['safe' => true, 'score' => 0.0, 'labels' => [], 'error' => null];

        if (empty($imageInput)) {
            return $safe;
        }

        $script = $this->getScript();
        if (! file_exists($script)) {
            Log::warning('[ImageChecker] Script não encontrado: ' . $script);
            return $safe;
        }

        $bin = $this->getBin();
        $cmd = PHP_OS_FAMILY === 'Windows'
            ? "\"{$bin}\" \"{$script}\" " . escapeshellarg($imageInput)
            : escapeshellarg($bin) . ' ' . escapeshellarg($script) . ' ' . escapeshellarg($imageInput);

        $output = shell_exec($cmd . ' 2>/dev/null');

        if (empty($output)) {
            Log::warning('[ImageChecker] Sem resposta para: ' . $imageInput);
            return $safe;
        }

        $result = json_decode(trim($output), true);

        if (! is_array($result)) {
            Log::warning('[ImageChecker] JSON inválido: ' . substr($output, 0, 200));
            return $safe;
        }

        return [
            'safe'   => (bool) ($result['safe']   ?? true),
            'score'  => (float) ($result['score']  ?? 0.0),
            'labels' => (array) ($result['labels'] ?? []),
            'error'  => $result['error'] ?? null,
        ];
    }

    /**
     * Retorna true se a imagem for segura (ou análise indisponível).
     */
    public function isSafe(string $imageInput): bool
    {
        return $this->check($imageInput)['safe'];
    }
}
