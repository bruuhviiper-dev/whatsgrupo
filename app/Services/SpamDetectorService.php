<?php

namespace App\Services;

class SpamDetectorService
{
    /**
     * Palavras frequentemente usadas em spam.
     */
    protected array $spamWords = [
        'ganhe dinheiro', 'clique aqui', 'promoção imperdível', 'grátis', 'gratis',
        'urgente', 'última chance', 'ultima chance', 'aproveite agora', 'renda extra',
        'resultado garantido', 'fique rico', 'apenas hoje', 'não perca', 'nao perca',
        'pix', 'renda online', 'trabalhe em casa', 'dinheiro fácil', 'dinheiro facil',
        'ganhos diários', 'ganhos diarios', 'lucro garantido', 'sorteio', 'aposte',
        'tiger', 'cassino', 'bet', 'investimento', 'roleta', 'bônus', 'bonus', 'vip',
        'curso gratuito'
    ];

    /**
     * Analisa a mensagem e retorna um score e sugestões.
     */
    public function analyze(string $text): array
    {
        $suggestions = [];
        $score = 100; // Começa com pontuação máxima (Seguro)
        
        $totalLength = mb_strlen($text);
        if ($totalLength === 0) {
            return [
                'score' => 0,
                'classification' => 'Atenção',
                'suggestions' => ['A mensagem está vazia.'],
                'indicators' => []
            ];
        }

        // 1. Quantidade de links (e urls curtas)
        $linksCount = preg_match_all('/https?:\/\/[^\s]+/', $text, $matches);
        $shortUrlsCount = 0;
        if ($linksCount > 0) {
            foreach ($matches[0] as $link) {
                if (preg_match('/(bit\.ly|tinyurl|t\.co|goo\.gl|is\.gd|cutt\.ly|lnkd\.in)/i', $link)) {
                    $shortUrlsCount++;
                }
            }
        }

        if ($linksCount > 2) {
            $score -= 20;
            $suggestions[] = 'Remover links excessivos (mais de 2 detectados).';
        } elseif ($linksCount > 0) {
            $score -= 5;
        }

        if ($shortUrlsCount > 0) {
            $score -= 15;
            $suggestions[] = 'Evitar o uso de URLs encurtadas (como bit.ly ou cutt.ly), pois reduzem a confiança.';
        }

        // 2. Quantidade de Emojis
        $emojisCount = preg_match_all('/[\x{1F600}-\x{1F64F}\x{1F300}-\x{1F5FF}\x{1F680}-\x{1F6FF}\x{1F700}-\x{1F77F}\x{1F780}-\x{1F7FF}\x{1F800}-\x{1F8FF}\x{1F900}-\x{1F9FF}\x{1FA00}-\x{1FA6F}\x{1FA70}-\x{1FAFF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}]/u', $text);
        if ($emojisCount > 5) {
            $score -= 10;
            $suggestions[] = "Diminuir o número de emojis ({$emojisCount} detectados). O excesso pode ser visto como spam.";
        }

        // 3. Uso excessivo de letras maiúsculas
        $uppercaseCount = preg_match_all('/[A-ZÁÉÍÓÚÀÂÊÔÃÕÇ]/u', $text);
        $lettersCount = preg_match_all('/[a-zA-Záéíóúàâêôãõç]/u', $text);
        if ($lettersCount > 0) {
            $uppercaseRatio = $uppercaseCount / $lettersCount;
            if ($uppercaseRatio > 0.4 && $totalLength > 20) {
                $score -= 15;
                $suggestions[] = 'Reduzir o uso de texto em CAIXA ALTA (Caps Lock).';
            }
        }

        // 4. Uso excessivo de exclamações
        $exclamationCount = substr_count($text, '!');
        if ($exclamationCount > 3) {
            $score -= 10;
            $suggestions[] = 'Evitar o uso excessivo de pontos de exclamação (!!!).';
        }

        // 5. Palavras consideradas spam
        $foundSpamWords = [];
        $lowerText = mb_strtolower($text);
        foreach ($this->spamWords as $word) {
            if (strpos($lowerText, $word) !== false) {
                $foundSpamWords[] = $word;
            }
        }

        if (count($foundSpamWords) > 0) {
            $score -= (count($foundSpamWords) * 15);
            $suggestions[] = 'Evitar palavras promocionais ou de gatilho excessivas: ' . implode(', ', array_slice($foundSpamWords, 0, 3)) . (count($foundSpamWords) > 3 ? '...' : '.');
        }

        // 6. Tamanho da mensagem muito curto ou muito longo
        if ($totalLength < 15) {
            $score -= 10;
            $suggestions[] = 'Mensagem muito curta pode não ser atrativa para os usuários.';
        } elseif ($totalLength > 1000) {
            $score -= 10;
            $suggestions[] = 'Sua mensagem está muito longa. Textos menores costumam ter melhor conversão no WhatsApp.';
        }

        // 7. Repetição excessiva de caracteres (ex: oiiiiiii)
        if (preg_match('/(.)\1{4,}/', $text)) {
            $score -= 10;
            $suggestions[] = 'Evitar repetição excessiva de caracteres (exemplo: "muitoooo").';
        }

        // Determinar classificação
        $score = max(0, min(100, $score)); // Garante limite entre 0 e 100
        
        $classification = 'Seguro';
        $colorClass = 'text-green-600 bg-green-50 border-green-200';
        $icon = 'check-badge';

        if ($score < 50) {
            $classification = 'Alto Risco';
            $colorClass = 'text-red-600 bg-red-50 border-red-200';
            $icon = 'exclamation-triangle';
        } elseif ($score < 80) {
            $classification = 'Atenção';
            $colorClass = 'text-amber-600 bg-amber-50 border-amber-200';
            $icon = 'exclamation-circle';
        }

        // Montar indicadores visuais (Breakdown)
        $indicators = [];
        if ($linksCount <= 1) {
            $indicators[] = ['icon' => 'check', 'text' => 'Poucos links', 'type' => 'success'];
        } else {
            $indicators[] = ['icon' => 'warning', 'text' => 'Muitos links', 'type' => 'warning'];
        }

        if (count($foundSpamWords) === 0) {
            $indicators[] = ['icon' => 'check', 'text' => 'Sem palavras suspeitas', 'type' => 'success'];
        } else {
            $indicators[] = ['icon' => 'warning', 'text' => 'Palavras promocionais', 'type' => 'danger'];
        }

        if ($emojisCount <= 5) {
            $indicators[] = ['icon' => 'check', 'text' => 'Quantidade aceitável de emojis', 'type' => 'success'];
        } else {
            $indicators[] = ['icon' => 'warning', 'text' => 'Excesso de emojis', 'type' => 'warning'];
        }

        if (empty($suggestions) && $score >= 90) {
            $suggestions[] = 'Sua mensagem está com ótima legibilidade e nenhum gatilho forte de spam detectado.';
        }

        return [
            'score' => $score,
            'classification' => $classification,
            'colorClass' => $colorClass,
            'icon' => $icon,
            'suggestions' => $suggestions,
            'indicators' => $indicators
        ];
    }
}
