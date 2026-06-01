<?php

namespace App\Http\Controllers;

use App\Models\StatusPhrase;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Controller responsável pela Seção de Frases para Status.
 */
class StatusPhraseController extends Controller
{
    /**
     * Exibe a página principal de frases com as categorias e as frases mais populares.
     */
    private function getExtendedCategories()
    {
        return [
            'amor'      => ['label' => 'Amor', 'slug' => 'amor', 'desc' => 'Frases românticas para status e legendas', 'icon' => 'heroicon-o-heart', 'color' => 'text-slate-500'],
            'amizade'   => ['label' => 'Amizade', 'slug' => 'amizade', 'desc' => 'Frases de carinho para amigos', 'icon' => 'heroicon-o-users', 'color' => 'text-slate-500'],
            'motivacao' => ['label' => 'Motivação', 'slug' => 'motivacao', 'desc' => 'Frases de superação, força e foco', 'icon' => 'heroicon-o-bolt', 'color' => 'text-slate-500'],
            'engracado' => ['label' => 'Engraçado', 'slug' => 'engracado', 'desc' => 'Status engraçados e tiradas divertidas', 'icon' => 'heroicon-o-face-smile', 'color' => 'text-slate-500'],
            'reflexao'  => ['label' => 'Reflexão', 'slug' => 'reflexao', 'desc' => 'Frases profundas e reflexivas para pensar', 'icon' => 'heroicon-o-light-bulb', 'color' => 'text-slate-500'],
            
            // +15 Novas Categorias
            'academia'  => ['label' => 'Academia', 'slug' => 'academia', 'desc' => 'Frases de treino, foco e musculação', 'icon' => 'heroicon-o-fire', 'color' => 'text-slate-500'],
            'boa-noite' => ['label' => 'Boa Noite', 'slug' => 'boa-noite', 'desc' => 'Mensagens lindas de boa noite', 'icon' => 'heroicon-o-moon', 'color' => 'text-slate-500'],
            'bom-dia'   => ['label' => 'Bom Dia', 'slug' => 'bom-dia', 'desc' => 'Mensagens inspiradoras de bom dia', 'icon' => 'heroicon-o-sun', 'color' => 'text-slate-500'],
            'curtas'    => ['label' => 'Curtas', 'slug' => 'curtas', 'desc' => 'Frases curtas para recados e status', 'icon' => 'heroicon-o-chat-bubble-bottom-center-text', 'color' => 'text-slate-500'],
            'deus'      => ['label' => 'Deus', 'slug' => 'deus', 'desc' => 'Mensagens de fé e gratidão a Deus', 'icon' => 'heroicon-o-hand-raised', 'color' => 'text-slate-500'],
            'evangelica'=> ['label' => 'Evangélica', 'slug' => 'evangelica', 'desc' => 'Louvores e reflexões evangélicas', 'icon' => 'heroicon-o-book-open', 'color' => 'text-slate-500'],
            'gratidao'  => ['label' => 'Gratidão', 'slug' => 'gratidao', 'desc' => 'Agradecendo pela vida e bênçãos', 'icon' => 'heroicon-o-sparkles', 'color' => 'text-slate-500'],
            'indiretas' => ['label' => 'Indiretas', 'slug' => 'indiretas', 'desc' => 'Frases de indireta para quem merece', 'icon' => 'heroicon-o-megaphone', 'color' => 'text-slate-500'],
            'musicas'   => ['label' => 'Músicas', 'slug' => 'musicas', 'desc' => 'Trechos de músicas para legendar fotos', 'icon' => 'heroicon-o-musical-note', 'color' => 'text-slate-500'],
            'sozinha'   => ['label' => 'Sozinha', 'slug' => 'sozinha', 'desc' => 'Textos para momentos a sós e amor-próprio', 'icon' => 'heroicon-o-user', 'color' => 'text-slate-500'],
            'tristes'   => ['label' => 'Tristes', 'slug' => 'tristes', 'desc' => 'Desabafos, dor e frases tristes', 'icon' => 'heroicon-o-cloud', 'color' => 'text-slate-500'],
            'visao'     => ['label' => 'Visão', 'slug' => 'visao', 'desc' => 'Frases de visão, progresso e favela', 'icon' => 'heroicon-o-eye', 'color' => 'text-slate-500'],
            'familia'   => ['label' => 'Família', 'slug' => 'familia', 'desc' => 'Homenagens e amor em família', 'icon' => 'heroicon-o-home', 'color' => 'text-slate-500'],
            'falsidade' => ['label' => 'Falsidade', 'slug' => 'falsidade', 'desc' => 'Frases sobre amizades falsas e inveja', 'icon' => 'heroicon-o-face-frown', 'color' => 'text-slate-500'],
            'maloka'    => ['label' => 'Maloka', 'slug' => 'maloka', 'desc' => 'Frases de cria e maloka para status', 'icon' => 'heroicon-o-star', 'color' => 'text-slate-500'],
        ];
    }

    public function index(Request $request)
    {
        $categories = $this->getExtendedCategories();

        $phrases = StatusPhrase::aprovadas()
            ->orderBy('likes', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('phrases.index', compact('categories', 'phrases'));
    }

    public function category(Request $request, string $category)
    {
        $categories = $this->getExtendedCategories();
        
        if (!array_key_exists($category, $categories)) {
            return redirect()->route('phrases.index')
                ->with('error', 'Categoria de frases inválida.');
        }

        $phrases = StatusPhrase::aprovadas()
            ->where('category', $category)
            ->orderBy('likes', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $categoryName = $categories[$category]['label'];

        return view('phrases.category', compact('phrases', 'category', 'categoryName', 'categories'));
    }

    public function like(Request $request, StatusPhrase $phrase)
    {
        $ip = $request->ip();
        $sessionKey = "phrase_liked_{$phrase->id}";
        $cacheKey = "like_ip_{$ip}_phrase_{$phrase->id}";

        // Bloqueio duplo: Sessão e Cache por IP (impede refresh de burlar)
        if ($request->session()->has($sessionKey) || \Illuminate\Support\Facades\Cache::has($cacheKey)) {
            return response()->json([
                'success' => false,
                'message' => 'Você já curtiu esta frase!',
                'likes'   => $phrase->likes
            ], 422);
        }

        $phrase->increment('likes');
        $request->session()->put($sessionKey, true);
        \Illuminate\Support\Facades\Cache::put($cacheKey, true, now()->addDays(30)); // Trava por 30 dias por IP

        return response()->json([
            'success' => true,
            'message' => 'Curtida registrada com sucesso!',
            'likes'   => $phrase->likes
        ]);
    }

    public function create()
    {
        $categories = $this->getExtendedCategories();
        return view('phrases.create', compact('categories'));
    }

    public function submit(Request $request)
    {
        $categories = $this->getExtendedCategories();
        $categoryKeys = implode(',', array_keys($categories));

        $request->validate([
            'phrase'   => 'required|string|min:10|max:500|unique:status_phrases,phrase',
            'category' => 'required|string|in:' . $categoryKeys,
            'author'   => 'nullable|string|max:100',
        ], [
            'phrase.unique' => 'Essa frase já existe no nosso banco de dados. Que tal enviar uma diferente?'
        ]);

        $phraseText = $request->input('phrase');

        // Regex para detectar Emojis
        $hasEmojis = preg_match('/[\x{1F600}-\x{1F64F}\x{1F300}-\x{1F5FF}\x{1F680}-\x{1F6FF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}\x{1F900}-\x{1F9FF}\x{1FA70}-\x{1FAFF}]/u', $phraseText);
        
        if ($hasEmojis) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'O envio de emojis não é permitido nas frases. Por favor, remova-os e use apenas texto.');
        }

        // Filtro de palavras proibidas
        $prohibitedWords = config('prohibited_words.palavroes', []);
        $lowercasePhrase = Str::lower($phraseText);

        foreach ($prohibitedWords as $word) {
            if (Str::contains($lowercasePhrase, Str::lower($word))) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Sua frase contém termos não permitidos pelo nosso sistema de moderação.');
            }
        }

        $phrase = StatusPhrase::create([
            'phrase'   => e($phraseText),
            'author'   => $request->input('author') ? e($request->input('author')) : 'Anônimo',
            'category' => $request->input('category'),
            'likes'    => 0,
            'status'   => 'pendente',
        ]);

        $submittedPhrases = json_decode($request->cookie('submitted_phrases', '[]'), true);
        if (!is_array($submittedPhrases)) {
            $submittedPhrases = [];
        }
        $submittedPhrases[] = $phrase->id;
        \Illuminate\Support\Facades\Cookie::queue('submitted_phrases', json_encode($submittedPhrases), 60 * 24 * 365);

        return redirect()->back()
            ->with('success', 'Frase enviada com sucesso! Ela já está visível para todos os visitantes.');
    }

    public function myPhrases(Request $request)
    {
        $cookieData = $request->cookie('submitted_phrases', '[]');
        $ids = json_decode($cookieData, true) ?? [];
        
        if (!empty($ids)) {
            $phrases = StatusPhrase::whereIn('id', $ids)
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $phrases = collect();
        }

        $categories = $this->getExtendedCategories();

        return view('phrases.my-phrases', compact('phrases', 'categories'));
    }

    /**
     * Exclui uma frase enviada pelo próprio usuário
     */
    public function destroyMyPhrase(Request $request, StatusPhrase $phrase)
    {
        $cookieData = $request->cookie('submitted_phrases', '[]');
        $ids = json_decode($cookieData, true) ?? [];

        if (!in_array($phrase->id, $ids)) {
            return redirect()->route('phrases.myPhrases')->with('error', 'Acesso negado.');
        }

        $phrase->delete();

        // Remove do Cookie
        $ids = array_filter($ids, fn($id) => $id !== $phrase->id);
        \Illuminate\Support\Facades\Cookie::queue('submitted_phrases', json_encode(array_values($ids)), 60 * 24 * 365);

        // Se não tem mais nenhuma frase, redireciona para a home de frases
        if (empty($ids)) {
            return redirect()->route('phrases.index')->with('success', 'Sua última frase foi excluída com sucesso.');
        }

        return redirect()->route('phrases.myPhrases')->with('success', 'Frase excluída com sucesso!');
    }

    /**
     * Exibe uma frase individual para compartilhamento dedicado (SEO e links diretos).
     */
    public function show(StatusPhrase $statusPhrase)
    {
        $categories = $this->getExtendedCategories();
        $categoryName = $categories[$statusPhrase->category]['label'] ?? 'Frase';

        // Busca 5 frases relacionadas da mesma categoria
        $relatedPhrases = StatusPhrase::aprovadas()
            ->where('category', $statusPhrase->category)
            ->where('id', '!=', $statusPhrase->id)
            ->inRandomOrder()
            ->limit(5)
            ->get();

        $nextPhrase = $relatedPhrases->first()
            ?? StatusPhrase::aprovadas()->where('id', '!=', $statusPhrase->id)->inRandomOrder()->first()
            ?? $statusPhrase;

        return view('phrases.show', compact('statusPhrase', 'categoryName', 'categories', 'relatedPhrases', 'nextPhrase'));
    }
}
