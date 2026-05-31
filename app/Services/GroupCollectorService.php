<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Group;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * GroupCollectorService v3.0
 *
 * Orquestra a chamada ao coletor Python universal e persiste os grupos
 * respeitando TODAS as regras de negócio:
 *
 *  ✔ Deduplicação por hash canônico (imune a variações de URL)
 *  ✔ Validação de nome (3–100 chars) e descrição (20–1000 chars)
 *  ✔ Filtro de palavras proibidas
 *  ✔ Fallback de categoria para "outros"
 *  ✔ 3 regras fixas obrigatórias em todos os grupos
 *  ✔ Imagem padrão do WhatsApp quando não há foto
 *  ✔ Conversão de imagem para WebP (Intervention Image / GD / Imagick)
 */
class GroupCollectorService
{
    private string $logPath;

    /** Imagem padrão oficial do WhatsApp para grupos sem foto */
    private const WA_DEFAULT_IMG = 'https://static.whatsapp.net/rsrc.php/v3/yP/r/rYZqPCBaG70.png';

    /** 3 regras fixas obrigatórias */
    private const REGRAS_FIXAS =
        "1. Proibido conteúdo adulto, pornografia ou nudez.\n" .
        "2. Proibido spam, links suspeitos ou propaganda não autorizada.\n" .
        "3. Respeite todos os membros do grupo. Sem preconceito, bullying ou ofensas.";

    public function __construct()
    {
        $this->logPath = storage_path('logs/mineracao.log');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PONTO DE ENTRADA PÚBLICO
    // ─────────────────────────────────────────────────────────────────────────

    public function collect(): int
    {
        $this->log('=' . str_repeat('=', 64), 'START');
        $this->log('WHATSGRUPOS COLETOR v3.0 – INICIANDO COLETA', 'START');
        $start = microtime(true);

        $categories = Category::all(['id', 'name', 'slug']);
        if ($categories->isEmpty()) {
            $this->log('Nenhuma categoria cadastrada. Abortando.', 'ERROR');
            return 0;
        }

        $this->log("Categorias disponíveis: {$categories->count()}");
        $categories->each(fn ($c) => $this->log("  → {$c->name} [{$c->slug}]"));

        // Chama o coletor Python
        $grupos = $this->executarPython($categories->toArray());
        if (empty($grupos)) {
            $this->log('Nenhum resultado retornado pelo Python.', 'WARNING');
            return 0;
        }

        $this->log('Grupos coletados pelo Python: ' . count($grupos));

        $importados   = 0;
        $duplicados   = 0;
        $invalidos    = 0;
        $semCategoria = 0;
        $proibidos    = 0;

        foreach ($grupos as $idx => $item) {
            $resultado = $this->processarItem($idx, $item, $categories);
            match ($resultado) {
                'ok'           => $importados++,
                'duplicado'    => $duplicados++,
                'invalido'     => $invalidos++,
                'sem_categoria'=> $semCategoria++,
                'proibido'     => $proibidos++,
                default        => null,
            };
        }

        $elapsed = round(microtime(true) - $start, 2);
        $this->log('── RESUMO ──', 'SUMMARY');
        $this->log("Importados     : {$importados}",   'SUMMARY');
        $this->log("Duplicados     : {$duplicados}",   'SUMMARY');
        $this->log("Inválidos      : {$invalidos}",    'SUMMARY');
        $this->log("Sem categoria  : {$semCategoria}", 'SUMMARY');
        $this->log("Com palavrão   : {$proibidos}",    'SUMMARY');
        $this->log("Tempo total    : {$elapsed}s",     'SUMMARY');
        $this->log('=' . str_repeat('=', 64), 'END');

        return $importados;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // EXECUÇÃO DO SCRIPT PYTHON
    // ─────────────────────────────────────────────────────────────────────────

    private function executarPython(array $categoriesArray): array
    {
        $pythonBin  = env('PYTHON_BIN', PHP_OS_FAMILY === 'Windows' ? 'python' : 'python3');
        $scriptPath = base_path('python-service/collector/group_collector.py');

        if (! file_exists($scriptPath)) {
            $this->log("Script Python não encontrado: {$scriptPath}", 'ERROR');
            return [];
        }

        $isWindows = PHP_OS_FAMILY === 'Windows';
        $cmd = $isWindows
            ? "\"{$pythonBin}\" \"{$scriptPath}\""
            : escapeshellarg($pythonBin) . ' ' . escapeshellarg($scriptPath);

        $this->log("Executando: {$cmd}");

        $descriptors = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];

        $process = proc_open($cmd, $descriptors, $pipes);
        if (! is_resource($process)) {
            $this->log('Não foi possível abrir processo Python.', 'ERROR');
            return [];
        }

        fwrite($pipes[0], json_encode($categoriesArray));
        fclose($pipes[0]);

        $stdout = stream_get_contents($pipes[1]);
        fclose($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[2]);
        $code = proc_close($process);

        if ($stderr) {
            $this->log('Python stderr: ' . substr($stderr, 0, 800), 'WARNING');
        }
        if ($code !== 0) {
            $this->log("Python encerrou com código {$code}.", 'ERROR');
            return [];
        }

        $decoded = json_decode($stdout, true);
        if (! is_array($decoded)) {
            $this->log('JSON inválido do Python: ' . substr($stdout, 0, 300), 'ERROR');
            return [];
        }

        return $decoded;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PROCESSAMENTO DE CADA ITEM
    // ─────────────────────────────────────────────────────────────────────────

    private function processarItem(int $idx, array $item, $categories): string
    {
        $link    = trim($item['link']           ?? '');
        $hash    = trim($item['hash']           ?? '');
        $catSlug = trim($item['category_slug']  ?? '');
        $nome    = trim($item['extracted_name'] ?? '');
        $desc    = trim($item['extracted_desc'] ?? '');
        $img     = trim($item['extracted_img']  ?? '');

        // ── Validação básica ──
        if (empty($link)) {
            $this->log("#{$idx}: link vazio.", 'WARNING');
            return 'invalido';
        }

        // ── Extrai hash canônico (fallback PHP) ──
        if (empty($hash)) {
            preg_match(
                '/(?:chat\.whatsapp\.com\/(?:invite\/)?|whatsapp\.com\/channel\/)([A-Za-z0-9_\-]{10,60})/i',
                $link,
                $m
            );
            $hash = $m[1] ?? null;
        }

        if (empty($hash)) {
            $this->log("#{$idx}: hash não extraível de {$link}", 'WARNING');
            return 'invalido';
        }

        // ── Monta canonical ──
        $isChannel = str_contains($link, 'channel');
        $canonical = $isChannel
            ? "https://whatsapp.com/channel/{$hash}"
            : "https://chat.whatsapp.com/{$hash}";

        // ── Deduplicação: hash + link canônico ──
        $jaExiste = Group::where('invite_hash', $hash)->exists()
            || Group::where('whatsapp_link', $canonical)->exists();

        if ($jaExiste) {
            $this->log("#{$idx}: DUPLICADO – {$canonical}", 'DEBUG');
            return 'duplicado';
        }

        // ── Categoria com fallback para "outros" ──
        $category = $categories->firstWhere('slug', $catSlug)
            ?? $categories->firstWhere('slug', 'outros');

        if (! $category) {
            $this->log("#{$idx}: sem categoria 'outros' no banco.", 'WARNING');
            return 'sem_categoria';
        }

        // ── Palavras proibidas ──
        $proibidas = config('prohibited_words.palavroes', []);
        foreach ($proibidas as $palavra) {
            if (stripos($nome, $palavra) !== false || stripos($desc, $palavra) !== false) {
                $this->log("#{$idx}: palavra proibida – {$canonical}", 'WARNING');
                return 'proibido';
            }
        }

        // ── Sanitização de nome ──
        if (mb_strlen($nome) < 3) {
            $this->log("#{$idx}: nome muito curto '{$nome}'. Ignorando.", 'WARNING');
            return 'invalido';
        }
        $nome = mb_substr($nome, 0, 100);

        // ── Sanitização de descrição ──
        if (mb_strlen($desc) < 20) {
            $desc = "Participe do grupo {$nome} da categoria {$category->name} no WhatsApp!";
        }
        $desc = mb_substr($desc, 0, 1000);

        // ── Imagem: download + conversão WebP ──
        // Se vazia, usa a imagem padrão do WhatsApp
        if (empty($img) || ! str_starts_with($img, 'http')) {
            $img = self::WA_DEFAULT_IMG;
        }
        $imagePath = $this->downloadImagem($img, $hash);

        // ── 3 Regras fixas obrigatórias ──
        $regras = self::REGRAS_FIXAS;

        // ── Cria o grupo como pendente ──
        Group::create([
            'category_id'     => $category->id,
            'name'            => $nome,
            'description'     => $desc,
            'rules'           => $regras,
            'whatsapp_link'   => $canonical,
            'invite_hash'     => $hash,
            'image_path'      => $imagePath,
            'submitter_email' => 'bot@whatsgrupos.com',
            'status'          => 'pending',
            'is_vip'          => false,
        ]);

        $this->log("#{$idx}: ✓ IMPORTADO – {$canonical} (cat: {$category->name})", 'SUCCESS');
        return 'ok';
    }

    // ─────────────────────────────────────────────────────────────────────────
    // DOWNLOAD E CONVERSÃO DE IMAGEM PARA WEBP
    // Estratégia em camadas: Intervention Image → GD → Imagick → salva original
    // ─────────────────────────────────────────────────────────────────────────

    private function downloadImagem(string $url, string $hash): ?string
    {
        try {
            $resp = Http::timeout(15)
                ->withHeaders(['User-Agent' => 'WhatsApp/2.24.5.77 A'])
                ->get($url);

            if (! $resp->ok()) {
                $this->log("Imagem HTTP {$resp->status()} – {$url}", 'WARNING');
                return null;
            }

            $body = $resp->body();
            if (strlen($body) < 100) {
                return null;
            }

            $path = "groups/{$hash}.webp";

            // Tentativa 1: Intervention Image (preferencial)
            if ($this->converterComIntervention($body, $path)) {
                return $path;
            }

            // Tentativa 2: GD nativo do PHP
            if ($this->converterComGd($body, $path)) {
                return $path;
            }

            // Tentativa 3: Imagick
            if ($this->converterComImagick($body, $path)) {
                return $path;
            }

            // Fallback: salva a imagem original sem conversão
            $ext  = $this->detectarExtensao($body, $url);
            $path = "groups/{$hash}.{$ext}";
            Storage::disk('public')->put($path, $body);
            $this->log("Imagem salva sem conversão WebP: {$path}", 'WARNING');
            return $path;

        } catch (\Exception $e) {
            $this->log("Erro ao processar imagem {$url}: " . $e->getMessage(), 'WARNING');
            return null;
        }
    }

    private function converterComIntervention(string $body, string $path): bool
    {
        try {
            // Suporta Intervention v2 e v3
            if (class_exists(\Intervention\Image\ImageManager::class)) {
                // v3
                $manager = new \Intervention\Image\ImageManager(
                    new \Intervention\Image\Drivers\Gd\Driver()
                );
                $image = $manager->read($body)->scale(width: 400)->toWebp(85);
                Storage::disk('public')->put($path, (string) $image);
                return true;
            }

            if (class_exists(\Intervention\Image\Facades\Image::class)) {
                // v2
                $image = \Intervention\Image\Facades\Image::make($body)
                    ->fit(400, 400)
                    ->encode('webp', 85);
                Storage::disk('public')->put($path, (string) $image);
                return true;
            }
        } catch (\Exception $e) {
            $this->log('Intervention falhou: ' . $e->getMessage(), 'DEBUG');
        }
        return false;
    }

    private function converterComGd(string $body, string $path): bool
    {
        if (! function_exists('imagecreatefromstring') || ! function_exists('imagewebp')) {
            return false;
        }
        try {
            $src = @imagecreatefromstring($body);
            if (! $src) {
                return false;
            }

            $w = imagesx($src);
            $h = imagesy($src);
            $size = min($w, $h, 400);
            $dst = imagecreatetruecolor($size, $size);

            // Mantém transparência
            imagealphablending($dst, false);
            imagesavealpha($dst, true);
            $transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
            imagefilledrectangle($dst, 0, 0, $size, $size, $transparent);

            imagecopyresampled($dst, $src, 0, 0, 0, 0, $size, $size, $w, $h);

            ob_start();
            imagewebp($dst, null, 85);
            $webpData = ob_get_clean();

            imagedestroy($src);
            imagedestroy($dst);

            if ($webpData) {
                Storage::disk('public')->put($path, $webpData);
                return true;
            }
        } catch (\Exception $e) {
            $this->log('GD falhou: ' . $e->getMessage(), 'DEBUG');
        }
        return false;
    }

    private function converterComImagick(string $body, string $path): bool
    {
        if (! class_exists(\Imagick::class)) {
            return false;
        }
        try {
            $imagick = new \Imagick();
            $imagick->readImageBlob($body);
            $imagick->resizeImage(400, 400, \Imagick::FILTER_LANCZOS, 1, true);
            $imagick->setImageFormat('webp');
            $imagick->setCompressionQuality(85);
            Storage::disk('public')->put($path, $imagick->getImageBlob());
            $imagick->clear();
            return true;
        } catch (\Exception $e) {
            $this->log('Imagick falhou: ' . $e->getMessage(), 'DEBUG');
        }
        return false;
    }

    private function detectarExtensao(string $body, string $url): string
    {
        $bytes = substr($body, 0, 12);
        if (str_starts_with($bytes, "\xFF\xD8\xFF"))          return 'jpg';
        if (str_starts_with($bytes, "\x89PNG"))                return 'png';
        if (str_starts_with($bytes, "GIF8"))                   return 'gif';
        if (str_starts_with($bytes, "RIFF") && str_contains($bytes, "WEBP")) return 'webp';
        if (str_starts_with($bytes, "\x00\x00\x00") && str_contains($bytes, "ftyp")) return 'avif';

        $ext = strtolower(pathinfo(parse_url($url, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION));
        return in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']) ? $ext : 'jpg';
    }

    // ─────────────────────────────────────────────────────────────────────────
    // LOGGING
    // ─────────────────────────────────────────────────────────────────────────

    private function log(string $msg, string $tipo = 'INFO'): void
    {
        $ts   = now()->format('Y-m-d H:i:s');
        $line = "[{$ts}] [{$tipo}] {$msg}" . PHP_EOL;
        File::append($this->logPath, $line);
        Log::channel('stack')->info("[Coletor] {$msg}");
    }
}
