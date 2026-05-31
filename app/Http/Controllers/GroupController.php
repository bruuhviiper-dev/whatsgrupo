<?php

namespace App\Http\Controllers;

use App\Mail\GroupSubmittedMail;
use App\Models\BoostOrder;
use App\Models\BoostUsage;
use App\Models\Category;
use App\Models\Group;
use App\Services\WhatsAppLinkValidator;
use App\Services\ReferralService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Intervention\Image\Facades\Image;

/**
 * Controller responsável por todas as operações públicas de grupos:
 * listagem por categoria, detalhe, envio, clique, busca, meus grupos e aplicação de boost.
 */
class GroupController extends Controller
{
    // -------------------------------------------------------------------------
    // Listagem por Categoria
    // -------------------------------------------------------------------------

    public function category(Category $category)
    {
        $category->load('sponsoredCategory');
        $currentPage = request()->get('page', 1);
        $cacheKey = "category_groups_{$category->slug}_page_{$currentPage}";

        // Caching por 5 minutos
        $data = Cache::remember($cacheKey, 300, function () use ($category, $currentPage) {
            // Grupos VIP da categoria (não expirados), mais recentes VIP primeiro
            $vipGroups = Group::with(['category', 'verifiedGroup'])
                ->approved()
                ->where('category_id', $category->id)
                ->notExpiredVip()
                ->orderBy('vip_expires_at', 'desc')
                ->get();

            // Grupos normais aprovados na categoria, ordenados por relevância (score)
            $normalGroups = Group::with(['category', 'verifiedGroup'])
                ->approved()
                ->where('category_id', $category->id)
                ->where(function ($q) {
                    $q->where('is_vip', false)->orWhere('vip_expires_at', '<=', now()->toDateTimeString());
                })
                ->orderBy('created_at', 'desc')
                ->get();

            $allGroups = $vipGroups->concat($normalGroups);

            // Paginação manual (30 por página, mesmo padrão da home)
            $perPage = 30;
            $groups = new \Illuminate\Pagination\LengthAwarePaginator(
                $allGroups->forPage($currentPage, $perPage),
                $allGroups->count(),
                $perPage,
                $currentPage,
                ['path' => request()->url(), 'query' => request()->query()]
            );

            $categories = Category::ordered()->withCount(['groups' => fn ($q) => $q->where('status', 'approved')])->get();

            return compact('groups', 'categories');
        });

        $groups = $data['groups'];
        $categories = $data['categories'];

        return view('category', compact('category', 'groups', 'categories'));
    }

    // -------------------------------------------------------------------------
    // Detalhe do Grupo
    // -------------------------------------------------------------------------

    public function show(string $id)
    {
        // Extrai a parte numerica inicial do ID (ex: 11-dicas-de-renda-extra vira 11)
        $realId = (int) explode('-', $id)[0];
        
        $group = Group::with('category')->find($realId);

        // Se o grupo não existir ou não estiver aprovado, redireciona para a home
        if (!$group || $group->status !== 'approved') {
            return redirect()->route('home');
        }

        // Incrementa o contador de visualizações
        $group->increment('views');

        // Grupos relacionados (mesma categoria, aprovados, exceto o atual) ordenados por score desc
        $related = Group::with(['category', 'verifiedGroup'])
            ->approved()
            ->where('category_id', $group->category_id)
            ->where('id', '!=', $group->id)
            ->orderByRaw('CASE WHEN is_vip = 1 AND vip_expires_at > datetime("now") THEN 1 ELSE 0 END DESC')
            ->orderByRaw('COALESCE(last_boosted_at, created_at) DESC')
            ->limit(8)
            ->get();

        $categories = Category::ordered()->get();

        // Busca buscas e assuntos SEO relacionados
        $relatedSeoPages = \App\Models\SeoPage::active()
            ->where('category_id', $group->category_id)
            ->whereNull('state')
            ->limit(8)
            ->get();

        if ($relatedSeoPages->isEmpty()) {
            $relatedSeoPages = \App\Models\SeoPage::active()
                ->whereNull('state')
                ->inRandomOrder()
                ->limit(8)
                ->get();
        }

        $latestBlogPosts = \App\Models\BlogPost::where('is_published', true)
            ->orderBy('created_at', 'desc')
            ->take(4)
            ->get();

        return view('group-detail', compact('group', 'related', 'categories', 'relatedSeoPages', 'latestBlogPosts'));
    }

    // -------------------------------------------------------------------------
    // Formulário de Envio de Grupo
    // -------------------------------------------------------------------------

    public function create()
    {
        $categories = Category::ordered()->get();
        return view('send-group', compact('categories'));
    }

    public function store(Request $request)
    {
        // Normaliza o link do WhatsApp antes de qualquer validação para unificar a hash e prevenir duplicidades por variações de URLs
        if ($request->has('whatsapp_link') && is_string($request->whatsapp_link)) {
            $request->merge([
                'whatsapp_link' => WhatsAppLinkValidator::normalizeLink($request->whatsapp_link)
            ]);
        }

        // Verifica unicidade pela hash do convite ANTES da validação, para cobrir variações não normalizadas (/invite/, /join/, etc.)
        $inviteHash = WhatsAppLinkValidator::extractHash($request->whatsapp_link ?? '');
        if ($inviteHash && \Illuminate\Support\Facades\Schema::hasColumn('groups', 'invite_hash')) {
            if (\App\Models\Group::where('invite_hash', $inviteHash)->exists()) {
                return back()->with('error', 'Este link já está cadastrado no diretório (mesmo grupo, variação de URL detectada).')->withInput();
            }
        }

        // Rate limit: máximo 3 envios por hora por IP (REMOVIDO PARA TESTES)
        // $rateLimitKey = 'send-group:' . $request->ip();
        // if (RateLimiter::tooManyAttempts($rateLimitKey, 3)) {
        //     $seconds = RateLimiter::availableIn($rateLimitKey);
        //     return back()->with('error', "Você atingiu o limite de envios. Tente novamente em {$seconds} segundos.")->withInput();
        // }
        // RateLimiter::hit($rateLimitKey, 3600); // Decaimento de 1 hora

        // Validação dos campos do formulário
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name'        => ['required', 'string', 'min:3', 'max:100', function ($attr, $val, $fail) {
                foreach (config('prohibited_words.palavroes', []) as $word) {
                    if (stripos($val, $word) !== false) {
                        $fail('O nome do grupo contém termos não permitidos.');
                        return;
                    }
                }
            }],
            'description' => ['required', 'string', 'min:20', 'max:1000', function ($attr, $val, $fail) {
                foreach (config('prohibited_words.palavroes', []) as $word) {
                    if (stripos($val, $word) !== false) {
                        $fail('A descrição contém termos não permitidos.');
                        return;
                    }
                }
            }],
            'rules'         => 'nullable|string|max:500',
            'whatsapp_link' => 'required|url|unique:groups,whatsapp_link',
            'selected_rules'=> 'required|array|min:1',
            'image'         => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'submitter_email' => 'nullable|email|max:255',
            'terms'         => 'accepted',
        ], [
            'category_id.required'    => 'Selecione uma categoria.',
            'name.required'           => 'Informe o nome do grupo.',
            'name.min'                => 'O nome deve ter pelo menos 3 caracteres.',
            'description.required'    => 'Escreva uma descrição para o grupo.',
            'description.min'         => 'A descrição deve ter pelo menos 20 caracteres.',
            'whatsapp_link.required'  => 'Informe o link do WhatsApp.',
            'whatsapp_link.unique'    => 'Este link já está cadastrado no diretório.',
            'selected_rules.required' => 'Selecione pelo menos uma das regras adicionais do grupo.',
            'selected_rules.min'      => 'Selecione pelo menos uma das regras adicionais do grupo.',
            'terms.accepted'          => 'Você precisa aceitar os termos para continuar.',
        ]);

        // Valida o link do WhatsApp via script Python
        $validator = new WhatsAppLinkValidator();
        $linkResult = $validator->validate($request->whatsapp_link);

        if (!$linkResult['valid']) {
            return back()->with('error', 'Link do WhatsApp inválido: ' . ($linkResult['error'] ?? 'formato incorreto.'))->withInput();
        }

        // Processa e salva a imagem como WebP 400x400 via Intervention Image v2
        $imagePath = null;
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            // 1) Imagem enviada pelo usuário: converte para WebP
            $img = Image::make($request->file('image'))
                ->fit(400, 400)         // Recorta e redimensiona para 400x400
                ->encode('webp', 85);   // Converte para WebP com qualidade 85

            $filename = 'groups/' . uniqid('grp_', true) . '.webp';
            \Illuminate\Support\Facades\Storage::disk('public')->put($filename, $img->getEncoded());
            $imagePath = $filename;
        } elseif (!empty($linkResult['image'])) {
            try {
                // 2) Imagem detectada automaticamente pelo validador (avatar do grupo no WhatsApp): baixa e converte para WebP
                $response = Http::timeout(10)->get($linkResult['image']);
                if ($response->successful()) {
                    $img = Image::make($response->body())
                        ->fit(400, 400)
                        ->encode('webp', 85);

                    $filename = 'groups/' . uniqid('grp_', true) . '.webp';
                    \Illuminate\Support\Facades\Storage::disk('public')->put($filename, $img->getEncoded());
                    $imagePath = $filename;
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning('[GroupController] Falha ao baixar imagem autodetectada: ' . $e->getMessage());
            }
        }

        // 3) Nenhuma imagem disponível: tenta baixar a imagem padrão do WhatsApp (og:image do site oficial)
        if (!$imagePath) {
            try {
                $waDefaultUrl = 'https://web.whatsapp.com/img/favicon/1x/favicon.png';
                // Tenta primeiro buscar a og:image da página de convite do WhatsApp
                $waPageUrl = $validated['whatsapp_link'];
                $pageResponse = Http::timeout(8)->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)',
                ])->get($waPageUrl);

                if ($pageResponse->successful()) {
                    $pageHtml = $pageResponse->body();
                    // Tenta extrair og:image da página do grupo
                    if (preg_match('/<meta[^>]+property=["\']og:image["\'][^>]+content=["\']([^"\']+)["\']/', $pageHtml, $ogMatch)) {
                        $waDefaultUrl = $ogMatch[1];
                    } elseif (preg_match('/<meta[^>]+content=["\']([^"\']+)["\'][^>]+property=["\']og:image["\']/', $pageHtml, $ogMatch)) {
                        $waDefaultUrl = $ogMatch[1];
                    }
                }

                $imgResponse = Http::timeout(10)->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36',
                ])->get($waDefaultUrl);

                if ($imgResponse->successful() && str_starts_with($imgResponse->header('Content-Type') ?? '', 'image/')) {
                    $img = Image::make($imgResponse->body())
                        ->fit(400, 400)
                        ->encode('webp', 85);

                    $filename = 'groups/' . uniqid('grp_', true) . '.webp';
                    \Illuminate\Support\Facades\Storage::disk('public')->put($filename, $img->getEncoded());
                    $imagePath = $filename;
                    \Illuminate\Support\Facades\Log::info('[GroupController] Imagem padrão do WhatsApp aplicada ao grupo.');
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning('[GroupController] Falha ao buscar imagem padrão do WhatsApp: ' . $e->getMessage());
            }
        }

        // Concatena as regras selecionadas com as regras personalizadas
        $selectedRules = $request->input('selected_rules', []);
        $customRules = $request->input('rules');
        $rulesText = implode("\n", $selectedRules);
        if (!empty($customRules)) {
            $rulesText .= "\n" . trim($customRules);
        }

        // Detecta automaticamente se o grupo é de apostas/gambling
        $isGambling = Group::detectGambling(
            $validated['name'],
            $validated['description'] ?? ''
        );

        // Cria o grupo com status pendente (salva a invite_hash para unicidade robusta, se a coluna já existir)
        $groupData = [
            'category_id'     => $validated['category_id'],
            'name'            => $validated['name'],
            'description'     => $validated['description'],
            'rules'           => $rulesText,
            'whatsapp_link'   => $validated['whatsapp_link'],
            'submitter_email' => $validated['submitter_email'] ?? null,
            'image_path'      => $imagePath,
            'status'          => 'pending',
            'is_gambling'     => $isGambling,
        ];

        // Inclui invite_hash apenas se a coluna já existir no banco (após rodar a migration)
        if ($inviteHash && \Illuminate\Support\Facades\Schema::hasColumn('groups', 'invite_hash')) {
            $groupData['invite_hash'] = $inviteHash;
        }

        $group = Group::create($groupData);

        // Armazena o ID do grupo nos cookies do navegador para gerenciamento automático (UX sem e-mail obrigatório)
        $submittedGroups = json_decode($request->cookie('submitted_groups', '[]'), true);
        if (!is_array($submittedGroups)) {
            $submittedGroups = [];
        }
        $submittedGroups[] = $group->id;
        \Illuminate\Support\Facades\Cookie::queue('submitted_groups', json_encode($submittedGroups), 60 * 24 * 365);

        // Envia e-mail de confirmação se o usuário informou o e-mail
        if ($group->submitter_email) {
            try {
                Mail::to($group->submitter_email)->send(new GroupSubmittedMail($group));
            } catch (\Exception $e) {
                // Falha no e-mail não deve impedir o cadastro do grupo
                \Illuminate\Support\Facades\Log::warning('[GroupController] Falha ao enviar e-mail: ' . $e->getMessage());
            }
        }

        return redirect()->route('send-group.create')->with('success', 'Grupo enviado com sucesso! Analisaremos em até 48 horas.');
    }

    // -------------------------------------------------------------------------
    // Redirecionamento para o Link do WhatsApp (contabiliza clique)
    // -------------------------------------------------------------------------

    public function click(Group $group, ReferralService $referralService)
    {
        abort_if($group->status !== 'approved', 404);

        // Incrementa o contador de cliques/entradas
        $group->increment('clicks');

        // Se há um código de indicação ativo na sessão, registra a conversão
        if (session()->has('active_referral_code')) {
            $code = session()->pull('active_referral_code');
            
            // Valida se a indicação de fato pertence ao grupo que está sendo clicado
            $referral = \App\Models\ReferralCode::where('code', $code)->first();
            if ($referral && $referral->group_id === $group->id) {
                $referralService->registerConversion($code);
            }
        }

        return redirect()->away($group->whatsapp_link);
    }

    // -------------------------------------------------------------------------
    // Busca de Grupos
    // -------------------------------------------------------------------------

    public function search(Request $request)
    {
        $query = $request->get('q', '');

        $groups = Group::with('category')
            ->approved()
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', '%' . $query . '%')
                  ->orWhere('description', 'like', '%' . $query . '%');
            })
            ->orderBy('is_vip', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(30)
            ->withQueryString();

        $categories = Category::ordered()->get();

        return view('search', compact('groups', 'query', 'categories'));
    }

    // -------------------------------------------------------------------------
    // Meus Grupos (listagem por e-mail)
    // -------------------------------------------------------------------------

    public function myGroups(Request $request)
    {
        $email = $request->input('email') ?? session('my_groups_email');

        if ($request->isMethod('post') && $request->has('email')) {
            $request->validate([
                'email' => 'required|email',
            ], [
                'email.required' => 'Informe seu e-mail.',
                'email.email'    => 'Informe um e-mail válido.',
            ]);

            session(['my_groups_email' => $request->email]);
            $email = $request->email;
        }

        if ($email) {
            $groups = Group::with('category')
                ->where('submitter_email', $email)
                ->orderBy('created_at', 'desc')
                ->get();
            
            $orders = \App\Models\BoostOrder::with('boostPackage')
                ->where('buyer_email', $email)
                ->where('payment_status', 'paid')
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            // Busca pelos IDs salvos nos cookies
            $cookieData = $request->cookie('submitted_groups', '[]');
            $ids = json_decode($cookieData, true);
            if (!is_array($ids)) {
                $ids = [];
            }

            if (!empty($ids)) {
                $groups = Group::with('category')
                    ->whereIn('id', $ids)
                    ->orderBy('created_at', 'desc')
                    ->get();
            } else {
                $groups = collect();
            }
            $email = null;
            $orders = collect();
        }

        return view('my-groups', [
            'groups'     => $groups,
            'orders'     => $orders,
            'email'      => $email,
            'categories' => Category::ordered()->get(),
        ]);
    }

    public function logoutMyGroups(Request $request)
    {
        $request->session()->forget('my_groups_email');
        return redirect()->route('my-groups')
            ->with('success', 'Você saiu da sua conta com sucesso.')
            ->withoutCookie('submitted_groups');
    }

    // -------------------------------------------------------------------------
    // Aplicação de Código Boost em um Grupo
    // -------------------------------------------------------------------------

    public function applyBoost(Request $request)
    {
        $request->validate([
            'group_id'   => 'required|exists:groups,id',
            'boost_code' => 'required|string|size:12',
            'email'      => 'required|email',
        ]);

        // Busca o grupo e verifica se pertence ao e-mail informado
        $group = Group::findOrFail($request->group_id);

        if ($group->status !== 'approved') {
            return response()->json(['success' => false, 'message' => 'Apenas grupos aprovados podem ser impulsionados.'], 422);
        }

        // Grupos de apostas/gambling não podem ser impulsionados — regra de negócio
        if ($group->is_gambling) {
            return response()->json(['success' => false, 'message' => 'Grupos de apostas e plataformas de jogos não podem ser impulsionados.'], 403);
        }

        if ($group->submitter_email !== $request->email) {
            return response()->json(['success' => false, 'message' => 'Este grupo não pertence ao e-mail informado.'], 403);
        }

        // Busca o pedido pelo código e verifica se tem impulsos disponíveis
        $order = BoostOrder::where('boost_code', strtoupper($request->boost_code))
            ->where('payment_status', 'paid')
            ->first();

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Código inválido ou pagamento não confirmado.'], 404);
        }

        if ($order->remaining_boosts <= 0) {
            return response()->json(['success' => false, 'message' => 'Este código não possui mais impulsos disponíveis.'], 422);
        }

        // Calcula a duração do VIP com base no pacote
        $durationHours = $order->boostPackage->duration_hours ?? 12;
        $expiresAt = now()->addHours($durationHours);

        // Se o grupo já tem VIP ativo, estende o prazo
        if ($group->is_vip && $group->vip_expires_at && $group->vip_expires_at->isFuture()) {
            $expiresAt = $group->vip_expires_at->addHours($durationHours);
        }

        // Registra o uso do impulso
        BoostUsage::create([
            'boost_order_id' => $order->id,
            'group_id'       => $group->id,
            'applied_at'     => now(),
            'expires_at'     => $expiresAt,
        ]);

        // Atualiza o grupo para VIP
        $group->update([
            'is_vip'          => true,
            'vip_expires_at'  => $expiresAt,
            'last_boosted_at' => now(),
        ]);

        // Incrementa o contador de impulsos usados no pedido
        $order->increment('boosts_used');

        return response()->json([
            'success'    => true,
            'message'    => "Impulso aplicado! Seu grupo ficará em destaque até " . $expiresAt->format('d/m/Y H:i') . '.',
            'expires_at' => $expiresAt->toIso8601String(),
        ]);
    }

    // -------------------------------------------------------------------------
    // Listagens Inteligentes (SEO de Cauda Longa)
    // -------------------------------------------------------------------------

    public function newest()
    {
        $title = 'Grupos de WhatsApp Novos';
        $description = 'Encontre os grupos de WhatsApp recém-adicionados no maior diretório do Brasil. Links ativos e atualizados.';

        $groups = Group::with(['category', 'verifiedGroup'])
            ->approved()
            ->orderBy('created_at', 'desc')
            ->paginate(30);

        $categories = Category::ordered()->get();

        return view('list-groups', compact('groups', 'title', 'description', 'categories'));
    }

    public function mostPopular()
    {
        $title = 'Grupos de WhatsApp Mais Populares';
        $description = 'Descubra os grupos de WhatsApp com o maior número de cliques e participantes. Entre nos melhores grupos.';

        $groups = Group::with(['category', 'verifiedGroup'])
            ->approved()
            ->orderBy('clicks', 'desc')
            ->paginate(30);

        $categories = Category::ordered()->get();

        return view('list-groups', compact('groups', 'title', 'description', 'categories'));
    }

    public function today()
    {
        $title = 'Novos Grupos de Hoje';
        $description = 'Confira os novos grupos de WhatsApp cadastrados nas últimas 24 horas no WhatsGrupos. Links ativos e verificados.';

        $groups = Group::with(['category', 'verifiedGroup'])
            ->approved()
            ->where('created_at', '>=', now()->subDay())
            ->orderBy('clicks', 'desc')
            ->paginate(30);

        $categories = Category::ordered()->get();

        return view('list-groups', compact('groups', 'title', 'description', 'categories'));
    }

    // -------------------------------------------------------------------------
    // Validação de Link via API interna (chamada pelo Alpine.js no formulário)
    // -------------------------------------------------------------------------

    public function validateLink(Request $request)
    {
        $link = $request->input('link', '');
        $validator = app(WhatsAppLinkValidator::class);
        $result = $validator->validate($link);
        return response()->json($result);
    }

    // -------------------------------------------------------------------------
    // Gerenciamento de "Meus Grupos" (Editar/Deletar)
    // -------------------------------------------------------------------------

    private function canManageMyGroup(Group $group)
    {
        $email = session('my_groups_email');
        if ($email && $group->submitter_email === $email) {
            return true;
        }

        $cookieData = request()->cookie('submitted_groups', '[]');
        $ids = json_decode($cookieData, true) ?? [];
        return in_array($group->id, $ids);
    }

    public function edit(Group $group)
    {
        if (!$this->canManageMyGroup($group)) {
            return redirect()->route('my-groups')->with('error', 'Acesso negado.');
        }

        if ($group->status !== 'pending') {
            return redirect()->route('my-groups')->with('error', 'Apenas grupos pendentes podem ser editados.');
        }

        $categories = Category::ordered()->get();
        return view('edit-group', compact('group', 'categories'));
    }

    public function update(Request $request, Group $group)
    {
        if (!$this->canManageMyGroup($group)) {
            return redirect()->route('my-groups')->with('error', 'Acesso negado.');
        }

        if ($group->status !== 'pending') {
            return redirect()->route('my-groups')->with('error', 'Apenas grupos pendentes podem ser editados.');
        }

        if ($request->has('whatsapp_link') && is_string($request->whatsapp_link)) {
            $request->merge([
                'whatsapp_link' => WhatsAppLinkValidator::normalizeLink($request->whatsapp_link)
            ]);
        }

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name'        => 'required|string|min:3|max:100',
            'description' => 'required|string|min:20|max:1000',
            'rules'       => 'nullable|string|max:500',
            'whatsapp_link' => 'required|url|unique:groups,whatsapp_link,' . $group->id,
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $validator = new WhatsAppLinkValidator();
        $linkResult = $validator->validate($request->whatsapp_link);

        if (!$linkResult['valid']) {
            return back()->with('error', 'Link do WhatsApp inválido: ' . ($linkResult['error'] ?? 'formato incorreto.'))->withInput();
        }

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            if ($group->image_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($group->image_path);
            }

            $img = Image::make($request->file('image'))
                ->fit(400, 400)
                ->encode('webp', 85);

            $filename = 'groups/' . uniqid('grp_', true) . '.webp';
            \Illuminate\Support\Facades\Storage::disk('public')->put($filename, $img->getEncoded());
            $group->image_path = $filename;
        }

        $group->category_id = $validated['category_id'];
        $group->name = $validated['name'];
        $group->description = $validated['description'];
        $group->rules = $validated['rules'] ?? null;
        $group->whatsapp_link = $validated['whatsapp_link'];
        $group->save();

        return redirect()->route('my-groups')->with('success', 'Grupo atualizado com sucesso!');
    }

    public function destroyMyGroup(Group $group)
    {
        if (!$this->canManageMyGroup($group)) {
            return redirect()->route('my-groups')->with('error', 'Acesso negado.');
        }

        if ($group->image_path) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($group->image_path);
        }

        $group->delete();

        return redirect()->route('my-groups')->with('success', 'Grupo removido com sucesso!');
    }
}
