<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Group;
use App\Models\ContactRequest;
use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

/**
 * Controller responsável pela página inicial do WhatsGrupos.
 * Mescla grupos VIP (topo) com grupos normais aprovados, com cache de 5 minutos.
 */
class HomeController extends Controller
{
    public function index(Request $request)
    {
        $currentPage = $request->get('page', 1);
        $tab = $request->get('tab', 'all');
        $cacheKey = "home_data_tab_{$tab}_page_{$currentPage}";

        // Armazena as consultas da Home em Cache por 5 minutos para altíssima performance
        $data = Cache::remember($cacheKey, 300, function () use ($request, $currentPage, $tab) {
            $query = Group::with(['category', 'verifiedGroup'])->approved();

            if ($tab === 'vip') {
                $allGroups = Group::with(['category', 'verifiedGroup'])
                    ->approved()
                    ->notExpiredVip()
                    ->orderByRaw('COALESCE(last_boosted_at, created_at) DESC')
                    ->get();
            } elseif ($tab === 'popular') {
                $allGroups = Group::with(['category', 'verifiedGroup'])
                    ->approved()
                    ->orderBy('clicks', 'desc')
                    ->get();
            } elseif ($tab === 'novos') {
                $allGroups = Group::with(['category', 'verifiedGroup'])
                    ->approved()
                    ->orderBy('created_at', 'desc')
                    ->get();
            } else {
                // Default: VIP ativos no topo, depois normais por score desc
                $vipGroups = Group::with(['category', 'verifiedGroup'])
                    ->approved()
                    ->notExpiredVip()
                    ->orderByRaw('COALESCE(last_boosted_at, created_at) DESC')
                    ->get();

                $normalGroups = Group::with(['category', 'verifiedGroup'])
                    ->approved()
                    ->where(function ($q) {
                        $q->where('is_vip', false)
                          ->orWhere('vip_expires_at', '<=', now()->toDateTimeString());
                    })
                    ->orderBy('created_at', 'desc')
                    ->get();

                $allGroups = $vipGroups->concat($normalGroups);
            }

            $perPage = 42;
            $groups = new \Illuminate\Pagination\LengthAwarePaginator(
                $allGroups->forPage($currentPage, $perPage),
                $allGroups->count(),
                $perPage,
                $currentPage,
                ['path' => $request->url(), 'query' => $request->query()]
            );
            // Categorias para o menu lateral
            $categories = Category::ordered()->withCount(['groups' => function ($q) {
                $q->where('status', 'approved');
            }])->get();

            // Busca 12 assuntos e páginas SEO especiais aleatórias
            $seoPages = \App\Models\SeoPage::active()
                ->whereNull('state')
                ->inRandomOrder()
                ->limit(12)
                ->get();

            // Busca os últimos 4 posts do blog
            $latestBlogPosts = BlogPost::where('is_published', true)
                ->orderBy('created_at', 'desc')
                ->limit(4)
                ->get();

            return compact('groups','categories', 'seoPages', 'latestBlogPosts');
        });

        $groups = $data['groups'];
        $categories = $data['categories'];
        $seoPages = $data['seoPages'] ?? collect();
        $latestBlogPosts = $data['latestBlogPosts'] ?? collect();

        return view('home', compact('groups','categories', 'seoPages', 'latestBlogPosts'));
    }

    /**
     * Exibe a página pública de publicidade e contato.
     */
    public function advertise(Request $request)
    {
        $categories = Category::ordered()->get();
        return view('anuncie', compact('categories'));
    }

    /**
     * Processa o envio do formulário de contato / publicidade.
     */
    public function submitAdvertise(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|min:3|max:100',
            'email'   => 'required|email|max:150',
            'subject' => 'required|string|in:Publicidade,Dúvida,Suporte,Denúncia,Outros',
            'message' => 'required|string|min:10|max:2000',
        ]);

        $messageText = $request->input('message');

        // Filtro de palavras proibidas básico
        $prohibitedWords = config('prohibited_words.palavroes', []);
        $lowercaseMessage = Str::lower($messageText);

        foreach ($prohibitedWords as $word) {
            if (Str::contains($lowercaseMessage, Str::lower($word))) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Sua mensagem contém termos impróprios ou não permitidos pela nossa moderação.');
            }
        }

        ContactRequest::create([
            'name'    => e($request->input('name')),
            'email'   => e($request->input('email')),
            'subject' => e($request->input('subject')),
            'message' => e($messageText),
            'is_read' => false,
        ]);

        return redirect()->back()
            ->with('success', '🎉 Sua mensagem foi enviada com sucesso! Nossa equipe de atendimento comercial/suporte retornará o contato por e-mail em até 24 horas úteis.');
    }
}
