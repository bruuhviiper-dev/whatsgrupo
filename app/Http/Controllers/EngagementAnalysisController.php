<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\GroupAnalysis;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

use App\Services\WhatsAppLinkValidator;

class EngagementAnalysisController extends Controller
{
    /**
     * Exibe o formulário da ferramenta.
     */
    public function create()
    {
        $categories = Category::ordered()->get();
        return view('tools.engagement.create', compact('categories'));
    }

    /**
     * Recebe os dados, simula uma análise e salva no banco.
     */
    public function store(Request $request)
    {
        $request->validate([
            'group_name' => 'required|string|max:200',
            'category_id' => 'required|exists:categories,id',
        ]);

        $category = Category::findOrFail($request->category_id);
        $groupName = $request->group_name;

        // Se o usuário colou um link do WhatsApp em vez do nome, tentamos extrair o nome real
        if (str_contains($groupName, 'chat.whatsapp.com') || str_contains($groupName, 'whatsapp.com/channel')) {
            $validator = new WhatsAppLinkValidator();
            $result = $validator->validate($groupName);
            if (!empty($result['name'])) {
                $groupName = $result['name'];
            }
        }

        // Gerador Estruturado: Baseado em um hash do nome para gerar resultados consistentes, independentemente da categoria
        $seed = md5(strtolower(trim($groupName)));
        
        // Simulação inteligente de dados
        $engagementPercent = $this->randomSeededInt($seed, 45, 98, 0);
        $healthScore = $this->randomSeededFloat($seed, 6.0, 9.9, 1);
        $msgsPerDay = $this->randomSeededInt($seed, 50, 1500, 2);
        
        $levels = ['Baixo', 'Médio', 'Alto', 'Muito Alto'];
        $levelIndex = 1;
        if ($engagementPercent >= 85) $levelIndex = 3;
        elseif ($engagementPercent >= 70) $levelIndex = 2;
        elseif ($engagementPercent < 50) $levelIndex = 0;
        
        $engagementLevel = $levels[$levelIndex];

        $trends = ['Crescendo', 'Crescendo', 'Crescendo', 'Estável', 'Estável', 'Declinando'];
        $growthTrend = $trends[$this->randomSeededInt($seed, 0, 5, 3)];

        $peakTimes = ['09h às 11h', '12h às 14h', '18h às 20h', '20h às 22h'];
        $peakTime = $peakTimes[$this->randomSeededInt($seed, 0, 3, 4)];

        // Textos Dinâmicos (Mock IA)
        $pros = [
            "Excelente retenção de membros novos.",
            "Tópicos mantêm-se relevantes na maior parte do tempo.",
            "Boa taxa de interação nos horários de pico.",
            "A maioria dos membros ativos compartilha mídias úteis.",
            "Ambiente considerado seguro e amigável.",
            "Crescimento orgânico forte detectado."
        ];
        
        $cons = [
            "Algum risco de mensagens off-topic.",
            "Pequenos picos de inatividade durante a tarde.",
            "Baixa interação nos finais de semana.",
            "Alguns links externos suspeitos identificados.",
            "Membros fantasmas não estão sendo removidos."
        ];

        // Selecionar 2 a 3 prós e 1 a 2 contras usando o seed
        $selectedPros = [];
        $selectedPros[] = $pros[$this->randomSeededInt($seed, 0, 2, 5)];
        $selectedPros[] = $pros[$this->randomSeededInt($seed, 3, 5, 6)];
        
        $selectedCons = [];
        $selectedCons[] = $cons[$this->randomSeededInt($seed, 0, 4, 7)];

        $publicSummary = "Com base em nossa análise preditiva para grupos de '{$category->name}', o grupo '{$groupName}' apresenta um potencial {$engagementLevel} de engajamento, com uma saúde geral avaliada em {$healthScore}/10. O crescimento aparenta estar {$growthTrend}.";

        // Salvar análise
        $analysis = GroupAnalysis::create([
            'group_name' => $groupName,
            'category' => $category->name,
            'engagement_level' => $engagementLevel,
            'engagement_percent' => $engagementPercent,
            'msgs_per_day' => $msgsPerDay,
            'peak_time' => $peakTime,
            'growth_trend' => $growthTrend,
            'health_score' => $healthScore,
            'pros' => $selectedPros,
            'cons' => $selectedCons,
            'public_summary' => $publicSummary,
        ]);

        return redirect()->route('tools.engagement.show', $analysis->uuid)
                         ->with('success', 'Análise gerada com sucesso!');
    }

    /**
     * Exibe o relatório gerado.
     */
    public function show($uuid)
    {
        $analysis = GroupAnalysis::where('uuid', $uuid)->firstOrFail();
        
        // Gerar dados adicionais dinâmicos baseados no UUID (consistentes e sem precisar de banco)
        $seed = md5($analysis->uuid);
        
        // Gráfico de Engajamento por Dia da Semana (0-100)
        $chartData = [
            'Seg' => $this->randomSeededInt($seed, 40, 95, 1),
            'Ter' => $this->randomSeededInt($seed, 50, 95, 2),
            'Qua' => $this->randomSeededInt($seed, 55, 100, 3),
            'Qui' => $this->randomSeededInt($seed, 60, 100, 4),
            'Sex' => $this->randomSeededInt($seed, 65, 100, 5),
            'Sáb' => $this->randomSeededInt($seed, 30, 85, 6),
            'Dom' => $this->randomSeededInt($seed, 20, 70, 7),
        ];
        
        // Nomes genéricos para preservar privacidade na análise
        $activeMembers = [];
        $numMembers = $this->randomSeededInt($seed, 3, 5, 8);
        
        for ($i = 0; $i < $numMembers; $i++) {
            $activeMembers[] = [
                'name' => 'Membro Oculto #' . $this->randomSeededInt($seed, 100, 999, 10 + $i),
                'msgs' => $this->randomSeededInt($seed, 10, 120, 30 + $i),
                'avatar' => 'https://api.dicebear.com/7.x/identicon/svg?seed=' . md5($seed . $i)
            ];
        }
        
        // Ordenar membros por mensagens (descendente)
        usort($activeMembers, function($a, $b) {
            return $b['msgs'] <=> $a['msgs'];
        });

        return view('tools.engagement.show', compact('analysis', 'chartData', 'activeMembers'));
    }

    /**
     * Helper para gerar Inteiro Pseudo-Randômico baseado em Seed e offset.
     */
    private function randomSeededInt($seed, $min, $max, $offset = 0)
    {
        $hash = substr(md5($seed . $offset), 0, 8);
        $dec = hexdec($hash);
        return $min + ($dec % (($max - $min) + 1));
    }

    /**
     * Helper para gerar Float Pseudo-Randômico.
     */
    private function randomSeededFloat($seed, $min, $max, $offset = 0)
    {
        $hash = substr(md5($seed . $offset), 0, 8);
        $dec = hexdec($hash);
        $ratio = $dec / 4294967295; // max hexdec for 8 chars
        return round($min + ($ratio * ($max - $min)), 1);
    }
}
