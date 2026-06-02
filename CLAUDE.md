# CLAUDE.md — WhatsGrupos

Plataforma de divulgação e ranqueamento de grupos de WhatsApp com SEO avançado,
bot de coleta automática, múltiplos gateways de pagamento e sistema de destaque VIP.

---

## Stack

- **Backend:** Laravel 12, PHP 8.2+
- **Frontend:** Blade + Alpine.js + Tailwind CSS + Vite
- **Banco de dados:** MySQL (produção via Docker), SQLite (dev local)
- **Cache / Filas:** Database driver (sem Redis)
- **Automação:** Python 3.10+ (scraper + link checker em `python-service/`)
- **Pagamentos:** Efí Bank (Pix), MercadoPago, Asaas, Stripe
- **Push:** Web Push (minishlink/web-push) via VAPID
- **Imagens:** Intervention Image v2 (GD), conversão para WebP
- **Testes:** PHPUnit 11
- **Deploy:** Docker Compose (produção) ou `start.sh` (bare-metal)

---

## Estrutura de pastas relevantes

```
app/
  Console/        → comandos artisan e agendamentos (schedule)
  Enums/          → enums tipados do domínio
  Http/
    Controllers/
      Admin/      → painel administrativo
    Controllers/  → controllers públicos
  Jobs/           → jobs de fila (coleta, score, push, sitemap)
  Mail/           → mailables
  Models/         → Eloquent models
  Observers/      → observers de model
  Repositories/   → repositórios de acesso a dados
  Services/       → lógica de negócio (pagamentos, coleta, scoring)
database/
  migrations/     → todas as migrations
  seeders/        → seeders (incluindo SeoPageSeeder com 1.400 combinações)
python-service/
  collector/      → group_collector.py, link_checker.py
resources/views/
  admin/          → views do painel admin
  components/     → componentes Blade reutilizáveis
routes/
  web.php         → rotas públicas + admin
  api.php         → endpoints API
```

---

## Models principais

| Model | Tabela | Descrição |
|---|---|---|
| `Group` | `groups` | Grupos de WhatsApp (status: pending/approved/rejected) |
| `Category` | `categories` | Categorias com slug |
| `User` | `users` | Usuários/donos de grupos |
| `BoostOrder` | `boost_orders` | Pedidos de destaque VIP pago |
| `BoostPackage` | `boost_packages` | Pacotes de boost disponíveis |
| `BoostUsage` | `boost_usages` | Uso ativo de boost (com expiração) |
| `SeoPage` | `seo_pages` | Páginas de cauda longa para SEO |
| `ReferralCode` | `referral_codes` | Links de indicação por grupo |
| `StatusPhrase` | `status_phrases` | Frases para status do WhatsApp |
| `JobLog` | `job_logs` | Log de execuções dos bots |
| `Setting` | `settings` | Configurações globais do sistema |
| `SponsoredCategory` | `sponsored_categories` | Categorias patrocinadas |
| `VerifiedGroup` | `verified_groups` | Grupos com selo de verificação |
| `Figurinha` | `figurinhas` | Figurinhas/stickers |

---

## Services

| Service | Responsabilidade |
|---|---|
| `GroupCollectorService` | Aciona o scraper Python via proc_open (JSON stdin/stdout) |
| `LinkCheckerService` | Verifica saúde dos links via Python |
| `GroupScoringService` | Calcula score ponderado: 40% cliques, 20% views, 30% recência, 10% perfil |
| `AsaasPaymentService` | Integração Asaas |
| `EfiPaymentService` | Integração Efí Bank (Pix) com polling assíncrono |
| `MercadoPagoPaymentService` | Integração MercadoPago |
| `StripePaymentService` | Integração Stripe |
| `WebPushService` | Envio de notificações push via VAPID |
| `ReferralService` | Lógica de recompensa por indicação (5 membros = 6h VIP grátis) |
| `SpamDetectorService` | Detecção de spam e apostas nos grupos |
| `FigurinhaService` | Processamento de figurinhas/stickers |

---

## Jobs agendados

| Job | Frequência | Fila |
|---|---|---|
| `CollectGroupsJob` | Semanal | coleta |
| `CheckLinksJob` | Diária | default |
| `RecalculateScoresJob` | A cada 6h | default |
| `ExpireBoostsJob` | A cada 1min | default |
| `GenerateSitemapJob` | Agendado | default |
| `SendNewGroupPushJob` | On demand (ao aprovar grupo) | default |

---

## Padrões de código

- **Controllers finos** — lógica de negócio sempre em Services
- **Form Requests** obrigatórios para toda validação (nunca validar no controller)
- **Repositories** para queries complexas (pasta `app/Repositories/`)
- **Nomenclatura:** inglês no código, português nos comentários de negócio
- **snake_case** em colunas de banco e migrations
- **Enums tipados** para status e tipos (pasta `app/Enums/`)
- **Observers** para side-effects de model (ex: log, notificação)
- **Jobs** para todo processamento assíncrono — nunca bloquear request
- Imagens sempre convertidas para **WebP** via Intervention Image v2
- Links de grupo normalizados por **hash único** (imune a variações de URL)
- Novos grupos entram sempre como `status = pending` (moderação obrigatória)

---

## Grupos — regras de negócio críticas

- Todo grupo novo entra como `pending`, precisa de aprovação no admin
- Status possíveis: `pending`, `approved`, `rejected`
- Bot coleta com email `bot@whatsgrupos.com`
- Grupos de apostas recebem `is_gambling = true` e não podem receber boost
- Hash de URL garante unicidade: `/invite/`, `/join/`, `/v/`, `/v=`, hash direto
- Canais usam prefixo `channel_` no hash
- Score recalculado a cada 6h: 40% cliques + 20% views + 30% recência + 10% perfil
- Referral: a cada 5 membros indicados → 6h VIP grátis automático + email de recompensa

---

## Rotas relevantes

```
GET  /                          → home (grupos VIP + normais mesclados por score)
GET  /categoria/{slug}          → listagem por categoria
GET  /grupo/{id}                → detalhe do grupo
GET  /g/{group}/entrar          → redirect para WhatsApp + incrementa clique
GET  /r/{code}                  → redirect de referral
GET  /buscar                    → busca por nome/descrição
GET  /grupos-novos              → listagem SEO
GET  /grupos-mais-populares     → listagem SEO
GET  /grupos-whatsapp/{slug}    → páginas de cauda longa (1.400 combinações)
GET  /blog, /blog/{slug}        → blog
GET  /widget.js, /widget/{cat}  → widget embarcável
GET  /frases, /frases/{cat}     → frases para status
GET  /analise-de-engajamento    → ferramenta de análise
GET  /gerador-de-regras         → ferramenta de geração de regras

Admin (prefixo /admin):
POST /admin/grupos/{id}/approve → aprovação de grupo
POST /admin/grupos/{id}/reject  → rejeição
GET  /admin/dashboard           → dashboard com Chart.js
GET  /admin/collector           → painel do bot Python
```

---

## Deploy

### Docker (produção)
```bash
cp .env.example .env   # configurar variáveis
docker compose up -d --build
```

O `entrypoint.sh` automaticamente: migrate → seed → cache → supervisord
Supervisord mantém: php-fpm + nginx + queue-default + queue-coleta + scheduler

### Bare-metal (dev/staging)
```bash
bash start.sh    # sobe tudo
bash stop.sh     # para tudo
bash reset.sh    # reset + limpeza de cache
bash reset.sh --hard  # também limpa a fila
```

### Comandos úteis
```bash
composer run dev              # servidor + fila + pail + vite
composer run test             # limpa config + roda testes
php artisan grupos:coletar    # coleta manual
php artisan queue:work coleta # worker da fila de coleta
php artisan schedule:work     # scheduler local
```

---

## Variáveis de ambiente críticas

```env
APP_ENV=production
APP_URL=https://seudominio.com

DB_CONNECTION=mysql
DB_HOST=mysql
DB_DATABASE=laravel
DB_USERNAME=laravel
DB_PASSWORD=secret

QUEUE_CONNECTION=database
CACHE_STORE=database

# Pagamentos
EFI_CLIENT_ID=
EFI_CLIENT_SECRET=
MERCADOPAGO_ACCESS_TOKEN=
ASAAS_API_KEY=
STRIPE_SECRET_KEY=

# Push
VAPID_PUBLIC_KEY=
VAPID_PRIVATE_KEY=
VAPID_SUBJECT=

# Python
PYTHON_BIN=/var/www/html/.venv/bin/python

# Coletor (opcional — limitar escopo)
COLLECTOR_MAX_DIRS=
COLLECTOR_MAX_GROUPS=
COLLECTOR_SKIP_SEARCH=
```

---

## Atenção ao trabalhar neste projeto

- Nunca remover a validação de `is_gambling` ao processar pagamentos de boost
- Sempre manter o hash de unicidade ao inserir novos grupos (ver `GroupCollectorService`)
- Migrations devem ser sempre backward-compatible (produção usa `--force`)
- O seeder `SeoPageSeeder` é idempotente — pode rodar em produção sem medo
- O Python service usa venv em `.venv/` — nunca instalar pacotes globalmente
- Cache de rotas e config ativo em produção — sempre rodar `artisan cache:clear` após mudanças
- Queries de listagem ordenam por `score DESC` — não alterar para `created_at` sem motivo
- Push notifications usam VAPID — as chaves do `.env` não podem ser rotacionadas sem migrar subscriptions
