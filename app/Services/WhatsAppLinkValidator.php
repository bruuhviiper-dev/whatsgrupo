<?php

namespace App\Services;

/**
 * Serviço responsável por invocar o script Python de validação de links do WhatsApp
 * via shell_exec, retornando os dados do grupo como array PHP.
 */
class WhatsAppLinkValidator
{
    /**
     * Normaliza o link do WhatsApp para um padrão único, extraindo a hash de convite.
     * Suporta grupos (chat.whatsapp.com) e canais (whatsapp.com/channel).
     * Remove variações como /invite/, /join/, /v/, /v= para garantir unicidade.
     */
    public static function normalizeLink(string $link): string
    {
        $link = trim($link);

        // Regex para Grupos de WhatsApp (suporta /invite/, /join/, /v/, /v= e hash direta)
        if (preg_match('/(?:chat\.whatsapp\.com\/(?:invite\/|join\/|v\/|v=)?)([a-zA-Z0-9_-]{10,})/i', $link, $matches)) {
            return 'https://chat.whatsapp.com/' . $matches[1];
        }

        // Regex para Canais de WhatsApp
        if (preg_match('/(?:whatsapp\.com\/channel\/)([a-zA-Z0-9@_-]{10,})/i', $link, $matches)) {
            return 'https://whatsapp.com/channel/' . $matches[1];
        }

        return $link;
    }

    /**
     * Extrai a hash/código único de um link do WhatsApp.
     * Retorna apenas a parte identificadora do grupo/canal, ignorando prefixos de URL.
     * Usado para garantir unicidade independente de variações de URL.
     */
    public static function extractHash(string $link): ?string
    {
        $link = trim($link);

        // Grupos: extrai hash após qualquer variação de prefixo
        if (preg_match('/chat\.whatsapp\.com\/(?:invite\/|join\/|v\/|v=)?([a-zA-Z0-9_-]{10,})/i', $link, $matches)) {
            return $matches[1];
        }

        // Canais: extrai código com prefixo para distinguir de grupos
        if (preg_match('/whatsapp\.com\/channel\/([a-zA-Z0-9@_-]{10,})/i', $link, $matches)) {
            return 'channel_' . $matches[1];
        }

        return null;
    }

    /**
     * Caminho absoluto para o script Python de validação.
     */
    protected string $scriptPath;

    /**
     * Caminho para o executável do Python, definido no .env via PYTHON_BIN.
     */
    protected string $pythonBin;

    public function __construct()
    {
        $this->scriptPath = base_path('python-service/validate_whatsapp.py');
        $this->pythonBin = config('app.python_bin', '/usr/bin/python3');
    }

    /**
     * Valida o link do WhatsApp chamando o script Python via shell_exec.
     *
     * @param  string  $link  O link do grupo ou canal do WhatsApp a ser validado
     * @return array{valid: bool, name: string|null, image: string|null, error: string|null, warning: string|null}
     */
    public function validate(string $link): array
    {
        // Normaliza o link antes de validar
        $link = self::normalizeLink($link);
        // Fallback seguro caso o Python ou o script não estejam disponíveis
        if (!$this->pythonAvailable()) {
            return $this->fallback('Executável Python não encontrado no servidor.');
        }

        if (!file_exists($this->scriptPath)) {
            return $this->fallback('Script de validação Python não encontrado.');
        }

        // Sanitiza o link para evitar injeção de comandos via shell
        $safeLink = escapeshellarg($link);
        $safePython = escapeshellcmd($this->pythonBin);
        $safeScript = escapeshellarg($this->scriptPath);

        // Monta o comando e redireciona stderr para /dev/null (ou NUL no Windows)
        $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
        $redirect = $isWindows ? '2>NUL' : '2>/dev/null';

        $command = "{$safePython} {$safeScript} {$safeLink} {$redirect}";

        // Executa o script Python e captura o JSON retornado no stdout
        $output = shell_exec($command);

        // Verifica se o script retornou alguma saída
        if (empty($output)) {
            return $this->fallback('Sem resposta do validador Python. Verifique se o Python está instalado.');
        }

        // Tenta decodificar o JSON retornado pelo script
        $result = json_decode(trim($output), true);

        if (!is_array($result)) {
            return $this->fallback('Resposta inválida do validador Python.');
        }

        return [
            'valid' => (bool) ($result['valid'] ?? false),
            'name' => $result['name'] ?? null,
            'image' => $result['image'] ?? null,
            'error' => $result['error'] ?? null,
            'warning' => $result['warning'] ?? null,
        ];
    }

    /**
     * Verifica se o executável Python está disponível no servidor.
     */
    protected function pythonAvailable(): bool
    {
        $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
        $check = $isWindows
            ? shell_exec("where \"{$this->pythonBin}\" 2>NUL")
            : shell_exec("which \"{$this->pythonBin}\" 2>/dev/null");

        return !empty($check);
    }

    /**
     * Retorna um array de fallback gracioso quando ocorre um erro de execução.
     * Considera o link como "potencialmente válido" para não bloquear o envio do grupo.
     *
     * @param  string  $reason  Motivo do fallback para logging interno
     */
    protected function fallback(string $reason): array
    {
        // Registra o motivo do fallback nos logs do Laravel para diagnóstico
        logger()->warning("[WhatsAppLinkValidator] Fallback ativado: {$reason}");

        return [
            'valid' => true,           // Não bloqueia o envio, apenas avisa
            'name' => null,
            'image' => null,
            'error' => null,
            'warning' => $reason,
        ];
    }
}
