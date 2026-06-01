<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FigurinhaAdminController;
use App\Http\Controllers\Admin\PhraseAdminController;
use App\Http\Controllers\Admin\GroupModerationController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\BoostController;
use App\Http\Controllers\Figurinha\FigurinhaController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ReferralController;
use App\Http\Controllers\SeoPageController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

// =============================================================================
// ROTAS PÚBLICAS
// =============================================================================

// Página inicial — lista grupos VIP + normais mesclados
Route::get('/', [HomeController::class, 'index'])->name('home');

// Listagem de grupos por categoria
Route::get('/categoria/{category:slug}', [GroupController::class, 'category'])->name('group.category');

// Detalhe de um grupo específico
Route::get('/grupo/{id}', [GroupController::class, 'show'])->name('group.show');

// Redireciona para o link do WhatsApp e incrementa o contador de cliques
Route::get('/g/{group}/entrar', [GroupController::class, 'click'])->name('group.click');

// Redirecionamento de links de indicações premium (referral)
Route::get('/r/{code}', [ReferralController::class, 'redirect'])->name('referral.redirect');

// Busca de grupos por nome e descrição
Route::get('/buscar', [GroupController::class, 'search'])->name('group.search');

// Listagens dinâmicas inteligentes (SEO cauda longa)
Route::get('/grupos-novos', [GroupController::class, 'newest'])->name('group.newest');
Route::get('/grupos-mais-populares', [GroupController::class, 'mostPopular'])->name('group.most-popular');
Route::get('/grupos-novos-hoje', [GroupController::class, 'today'])->name('group.today');

// Blog de WhatsApp (dicas e tutoriais)
Route::get('/blog', [\App\Http\Controllers\BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/categoria/{slug}', [\App\Http\Controllers\BlogController::class, 'category'])->name('blog.category');
Route::get('/blog/{slug}', [\App\Http\Controllers\BlogController::class, 'show'])->name('blog.show');

// Rota dinâmica para as páginas SEO de cauda longa
Route::get('/grupos-whatsapp/{seoPage:slug}', [SeoPageController::class, 'show'])->name('seo-page.show');

// Widget Embarcável
Route::get('/widget.js', [\App\Http\Controllers\WidgetController::class, 'script'])->name('widget.script');
Route::get('/widget/{category_slug?}', [\App\Http\Controllers\WidgetController::class, 'show'])->name('widget.show');
Route::get('/widget-gerador', [\App\Http\Controllers\WidgetController::class, 'generator'])->name('widget.generator');

// Seção de Frases para Status
Route::get('/frases', [\App\Http\Controllers\StatusPhraseController::class, 'index'])->name('phrases.index');
Route::get('/minhas-frases', [\App\Http\Controllers\StatusPhraseController::class, 'myPhrases'])->name('phrases.myPhrases');
Route::delete('/frases/{phrase}/delete', [\App\Http\Controllers\StatusPhraseController::class, 'destroyMyPhrase'])->name('phrases.destroyMyPhrase');
Route::get('/frases/frase/{statusPhrase}', [\App\Http\Controllers\StatusPhraseController::class, 'show'])->name('phrases.show');
Route::get('/frases/{category}', [\App\Http\Controllers\StatusPhraseController::class, 'category'])->name('phrases.category');
Route::post('/frases/curtir/{phrase}', [\App\Http\Controllers\StatusPhraseController::class, 'like'])->name('phrases.like');
Route::post('/frases/enviar', [\App\Http\Controllers\StatusPhraseController::class, 'submit'])->name('phrases.submit');
Route::get('/enviar-frase', [\App\Http\Controllers\StatusPhraseController::class, 'create'])->name('phrases.create');

// --- Nova Ferramenta: Analisador de Engajamento ---
Route::get('/ferramentas/analise-de-engajamento', [\App\Http\Controllers\EngagementAnalysisController::class, 'create'])->name('tools.engagement.create');
Route::post('/ferramentas/analise-de-engajamento', [\App\Http\Controllers\EngagementAnalysisController::class, 'store'])->name('tools.engagement.store');
Route::get('/analise/{uuid}', [\App\Http\Controllers\EngagementAnalysisController::class, 'show'])->name('tools.engagement.show');

// --- Nova Ferramenta: Gerador de Regras ---
Route::get('/ferramentas/gerador-de-regras', [\App\Http\Controllers\RulesGeneratorController::class, 'index'])->name('tools.rules.index');

// --- Novas Ferramentas ---
Route::get('/ferramentas/gerador-de-nomes', [\App\Http\Controllers\ToolsController::class, 'nameGenerator'])->name('tools.name-generator');
Route::get('/ferramentas/mensagem-de-boas-vindas', [\App\Http\Controllers\ToolsController::class, 'welcomeMessage'])->name('tools.welcome-message');
Route::get('/ferramentas/verificador-de-link', [\App\Http\Controllers\ToolsController::class, 'linkValidator'])->name('tools.link-validator');
Route::get('/ferramentas/gerador-de-enquete', [\App\Http\Controllers\ToolsController::class, 'pollGenerator'])->name('tools.poll-generator');
Route::get('/ferramentas/gerador-de-letras', [\App\Http\Controllers\ToolsController::class, 'fontsGenerator'])->name('tools.fonts-generator');

// Sorteios
Route::get('/ferramentas/gerador-de-sorteios', [\App\Http\Controllers\RaffleController::class, 'index'])->name('tools.raffle-generator');
Route::post('/ferramentas/gerador-de-sorteios', [\App\Http\Controllers\RaffleController::class, 'store'])->name('tools.raffle-generator.store');
Route::get('/sorteio/buscar', [\App\Http\Controllers\RaffleController::class, 'search'])->name('tools.raffle.search');
Route::post('/sorteio/{uuid}/email', [\App\Http\Controllers\RaffleController::class, 'sendEmail'])->name('tools.raffle.email');
Route::get('/sorteio/{uuid}', [\App\Http\Controllers\RaffleController::class, 'show'])->name('tools.raffle.show');

Route::get('/ferramentas/detector-de-spam', [\App\Http\Controllers\ToolsController::class, 'spamDetector'])->name('tools.spam-detector');
Route::post('/ferramentas/detector-de-spam/analisar', [\App\Http\Controllers\ToolsController::class, 'analyzeSpam'])->name('tools.spam-detector.analyze');

// Página comercial e de publicidade
Route::get('/anuncie', [HomeController::class, 'advertise'])->name('advertise');
Route::post('/anuncie', [HomeController::class, 'submitAdvertise'])->name('advertise.submit');

// Páginas Institucionais (Termos, Privacidade, FAQ, Contato)
Route::view('/termos-de-uso', 'pages.termos')->name('pages.termos');
Route::view('/politica-de-privacidade', 'pages.privacidade')->name('pages.privacidade');
Route::view('/faq', 'pages.faq')->name('pages.faq');
Route::view('/contato', 'pages.contato')->name('pages.contato');

// =============================================================================
// FIGURINHAS PARA WHATSAPP
// =============================================================================
Route::prefix('figurinhas')->name('figurinhas.')->group(function () {
    Route::get('/', [FigurinhaController::class, 'index'])->name('index');
    Route::get('/enviar', [FigurinhaController::class, 'create'])->name('create');
    Route::post('/enviar', [FigurinhaController::class, 'store'])->name('store');
    Route::get('/{slug}/baixar', [FigurinhaController::class, 'download'])->name('download');
    Route::get('/{slug}', [FigurinhaController::class, 'show'])->name('show');
});

// Formulário de envio de novo grupo (GET = formulário, POST = salvar)
Route::get('/enviar-grupo', [GroupController::class, 'create'])->name('send-group.create');
Route::post('/enviar-grupo', [GroupController::class, 'store'])->name('send-group.store');

// Meus Grupos — exibe lista automática baseada em cookies (GET) ou por busca de e-mail (POST)
Route::get('/meus-grupos', [GroupController::class, 'myGroups'])->name('my-groups');
Route::post('/meus-grupos', [GroupController::class, 'myGroups'])->name('my-groups.search');
Route::post('/meus-grupos/sair', [GroupController::class, 'logoutMyGroups'])->name('my-groups.logout');
Route::get('/meus-grupos/{group}/editar', [GroupController::class, 'edit'])->name('my-groups.edit');
Route::put('/meus-grupos/{group}', [GroupController::class, 'update'])->name('my-groups.update');
Route::delete('/meus-grupos/{group}', [GroupController::class, 'destroyMyGroup'])->name('my-groups.destroy');

// Aplicação de código de boost em um grupo (chamada AJAX pelo Alpine.js)
Route::post('/meus-grupos/aplicar-boost', [GroupController::class, 'applyBoost'])->name('group.apply-boost');

// =============================================================================
// ROTAS DE PACOTES VIP E PAGAMENTO
// =============================================================================

// Listagem de pacotes VIP disponíveis
Route::get('/pacotes-vip', [BoostController::class, 'packages'])->name('boost.packages');

// Página de checkout do pacote selecionado (GET = formulário, POST = processar)
Route::get('/pacotes-vip/{package:slug}', [BoostController::class, 'checkout'])->name('boost.checkout');
Route::post('/pacotes-vip/{package:slug}', [BoostController::class, 'processCheckout'])->name('boost.process-checkout');

// Novas rotas AJAX de Checkout na mesma tela (Embedded)
Route::post('/pacotes-vip/{package:slug}/checkout-stripe-embedded', [BoostController::class, 'checkoutStripeEmbedded'])->name('boost.checkout-stripe-embedded');
Route::post('/pacotes-vip/{package:slug}/checkout-asaas-pix', [BoostController::class, 'checkoutAsaasPix'])->name('boost.checkout-asaas-pix');
Route::post('/pacotes-vip/{package:slug}/checkout-mp-pix', [BoostController::class, 'checkoutMercadoPagoPix'])->name('boost.checkout-mp-pix');

// Verificação de domínio do Apple Pay (exigida pela Stripe para liberar o wallet no checkout embedded).
// Serve o arquivo gerado pela Stripe como text/plain com status 200, em /.well-known/...
Route::get('/.well-known/apple-developer-merchantid-domain-association', function () {
    $path = config('services.stripe.apple_pay_domain_association_path');

    abort_unless($path && is_file($path) && filesize($path) > 0, 404);

    return response(file_get_contents($path), 200, [
        'Content-Type' => 'text/plain',
    ]);
})->name('apple-pay.domain-association');

// Webhooks de pagamento
Route::post('/webhook/asaas', [BoostController::class, 'webhookAsaas'])->name('webhook.asaas');
Route::post('/webhook/efi', [BoostController::class, 'webhook'])->name('webhook.efi');
Route::post('/webhook/stripe', [BoostController::class, 'webhookStripe'])->name('webhook.stripe');
Route::post('/webhook/mercadopago', [BoostController::class, 'webhookMercadoPago'])->name('webhook.mercadopago');

// Página de sucesso após pagamento confirmado
Route::get('/pagamento/sucesso/{order}', [BoostController::class, 'success'])->name('boost.success');

// Polling de status do PIX
Route::get('/pagamento/pix-status/{order}', [BoostController::class, 'pixStatus'])->name('boost.pix-status');
Route::get('/pagamento/mp-pix-status/{order}', [BoostController::class, 'pixStatusMercadoPago'])->name('boost.mp-pix-status');

// =============================================================================
// ROTAS DO PAINEL DE ADMINISTRAÇÃO
// =============================================================================

// Login e logout (sem middleware — rotas públicas do admin)
Route::get('/admin/login', function () {
    return view('admin.login');
})->name('admin.login.form');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

// Grupo de rotas protegidas pelo middleware de autenticação do admin
Route::middleware('admin.auth')->prefix('admin')->name('admin.')->group(function () {

    // Dashboard principal
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Toggle rápido de tema (dark/light)
    Route::post('/toggle-theme', function (\Illuminate\Http\Request $request) {
        $new = $request->session()->get('admin_theme', 'light') === 'dark' ? 'light' : 'dark';
        $request->session()->put('admin_theme', $new);
        \App\Models\Setting::set('admin_theme', $new);
        return back();
    })->name('theme.toggle');

    // Perfil do Administrador
    Route::get('/perfil', [AdminProfileController::class, 'edit'])->name('profile');
    Route::post('/perfil', [AdminProfileController::class, 'update'])->name('profile.update');

    // Configurações (AdSense)
    Route::get('/settings',         [SettingsController::class, 'index'])->name('settings');
    Route::post('/settings',        [SettingsController::class, 'save'])->name('settings.save');
    Route::get('/settings/ads-txt', [SettingsController::class, 'downloadAdsTxt'])->name('settings.ads-txt');

    // Métricas e Gráficos de Analytics
    Route::get('/analytics', [\App\Http\Controllers\Admin\AnalyticsController::class, 'index'])->name('analytics');

    // Moderação de Figurinhas
    Route::prefix('figurinhas')->name('figurinhas.')->group(function () {
        Route::get('/', [FigurinhaAdminController::class, 'index'])->name('index');
        Route::post('/{figurinha}/aprovar', [FigurinhaAdminController::class, 'aprovar'])->name('aprovar');
        Route::post('/{figurinha}/rejeitar', [FigurinhaAdminController::class, 'rejeitar'])->name('rejeitar');
        Route::delete('/{figurinha}', [FigurinhaAdminController::class, 'destroy'])->name('destroy');
    });

    // Moderação de Frases de Status
    Route::prefix('frases')->name('phrases.')->group(function () {
        Route::get('/', [PhraseAdminController::class, 'index'])->name('index');
        Route::post('/{phrase}/aprovar', [PhraseAdminController::class, 'aprovar'])->name('aprovar');
        Route::post('/{phrase}/rejeitar', [PhraseAdminController::class, 'rejeitar'])->name('rejeitar');
        Route::delete('/{phrase}', [PhraseAdminController::class, 'destroy'])->name('destroy');
    });

    // Moderação de Grupos
    Route::prefix('grupos')->name('groups.')->group(function () {
        Route::get('/', [GroupModerationController::class, 'index'])->name('index');
        Route::get('/pendentes', [GroupModerationController::class, 'pending'])->name('pending');
        Route::post('/{group}/aprovar', [GroupModerationController::class, 'approve'])->name('approve');
        Route::post('/{group}/rejeitar', [GroupModerationController::class, 'reject'])->name('reject');
        Route::post('/{group}/gambling', [GroupModerationController::class, 'toggleGambling'])->name('gambling.toggle');
        Route::delete('/{group}', [GroupModerationController::class, 'destroy'])->name('destroy');
    });

    // Gerenciamento de Pedidos de Impulso
    Route::prefix('pedidos')->name('orders.')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::post('/{order}/reenviar', [OrderController::class, 'resendCode'])->name('resend');
    });

    // Coletor Automático de Grupos e Validador de Links
    Route::prefix('coletor')->name('collector.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\CollectorController::class, 'index'])->name('index');
        Route::post('/executar', [\App\Http\Controllers\Admin\CollectorController::class, 'run'])->name('run');
        Route::post('/verificar', [\App\Http\Controllers\Admin\CollectorController::class, 'checkLinks'])->name('check-links');
    });

    // Gerenciamento de Notícias do Blog
    Route::resource('blog', \App\Http\Controllers\Admin\BlogController::class)->except(['show']);
});

// =============================================================================
// ROTAS UTILITÁRIAS
// =============================================================================

// Sitemaps XML — servidos dinamicamente pelo SitemapController (Sitemap Index)
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/sitemap-static.xml', [SitemapController::class, 'static'])->name('sitemap.static');
Route::get('/sitemap-groups.xml', [SitemapController::class, 'groups'])->name('sitemap.groups');
Route::get('/sitemap-seo.xml', [SitemapController::class, 'seo'])->name('sitemap.seo');

// Robots.txt — exibido como view de texto puro
Route::get('/robots.txt', function () {
    return response()->view('robots', [], 200, ['Content-Type' => 'text/plain']);
})->name('robots');

Route::get('/ads.txt', function () {
    $clientId = \App\Models\Setting::get('adsense_client_id', 'ca-pub-XXXXXXXXXXXXXXXX');
    $content  = "google.com, {$clientId}, DIRECT, f08c47fec0942fa0\n";
    return response($content, 200, ['Content-Type' => 'text/plain']);
})->name('ads.txt');
