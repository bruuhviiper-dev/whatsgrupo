# 📱 WhatsGrupos — Plataforma Premium de Divulgação de Grupos de WhatsApp

O **WhatsGrupos** é uma plataforma robusta, de altíssima performance e design ultra-moderno desenvolvida em **Laravel 11**, integrada com **SQLite**, **Alpine.js**, **Tailwind CSS** e **Python**. 

A aplicação foi projetada para atração massiva de tráfego orgânico através de SEO avançado e viralização, contando com múltiplos canais de monetização e automação inteligente por bots nativos.

---

## 🚀 Principais Funcionalidades

### 📈 1. SEO Técnico Avançado (Cauda Longa)
* **Páginas de Cauda Longa (`seo_pages`):** Mais de 1.400 combinações exclusivas de páginas dinâmicas ("Categorias x Estados" e "Categorias x Termos extras") com parágrafos únicos gerados programaticamente.
* **Schema Markup (JSON-LD):** Componentes Blade (`schema-group` e `schema-list`) para estruturação rica de dados que melhoram a visualização no Google.
* **Sitemaps XML Segmentados:** Sitemap index com subdivisões para páginas estáticas, grupos aprovados e páginas de SEO, servidos dinamicamente sob cache.

### 🤖 2. Bots de Coleta e Verificação Automática (Python)
* **Scraper DuckDuckGo (`group_collector.py`):** Script autônomo Python que minera e extrai novos links de grupos do WhatsApp diretamente de motores de busca.
* **Link Checker (`link_checker.py`):** Verificador assíncrono que detecta e remove ou suspende automaticamente links de convite corrompidos, expirados ou revogados.
* **Agendamento Automático:** Jobs Laravel agendados para rodar de forma transparente via Cron.

### 🧠 3. Sistema de Ranqueamento Relevância (Score)
* **Algoritmo Inteligente:** Pontuação ponderada calculada a cada 6 horas com base em:
  * Cliques reais de entrada (40%)
  * Visualizações da página de detalhes (20%)
  * Recência temporal da listagem (30%)
  * Completude do perfil - imagens/regras (10%)
* A listagem geral prioriza os grupos com melhor pontuação de engajamento ativa.

### 💰 4. Múltiplos Fluxos de Monetização
* **Super VIP (Destaque Pago):** Integração Pix com o banco **Efí Bank** contendo polling assíncrono em tempo real. Grupos VIP contam com bordas douradas pulsantes exclusivas no topo do site.
* **Categorias Patrocinadas:** Banners comerciais horizontais configurados para exibição exclusiva no cabeçalho de categorias de alto tráfego.
* **Grupos Verificados (Selo Azul):** Assinatura mensal que confere selo azul oficial de verificação ao grupo, dobrando a conversão de entrada.

### 🔔 5. Notificações Web Push
* Inscrição integrada por Service Worker e banner Alpine.js inteligente que convida a inscrição após 30 segundos.
* Disparo automatizado em segundo plano de novos grupos na categoria preferida do usuário assim que aprovados pela moderação.

### 🔗 6. Viralização por Recompensas (Referral)
* Links de indicações exclusivos (`/r/{code}`) para proprietários de grupos.
* A cada **5 novos membros** atraídos pelo link de indicação, o grupo ganha automaticamente **6 horas de destaque VIP gratuito** (com aviso e e-mail premium de recompensa).

### 🔌 7. Widget Embarcável
* Código HTML/JS assíncrono gerável dinamicamente para que blogs e sites parceiros incorporem uma lista responsiva de 6 grupos VIP/Aprovados de categorias específicas.

### 💬 8. Seção de Frases para Status
* Banco de dados contendo **200 frases brasileiras reais** segmentadas em Amor, Amizade, Motivação, Engraçado e Reflexão.
* Copiar com um clique, curtidas assíncronas AJAX (Alpine.js) e compartilhamento rápido estruturado para o WhatsApp.

### 📊 9. Analytics Admin Avançado
* Dashboard integrada com gráficos ricos em tempo real (Chart.js via CDN) que exibem novas adesões, faturamento acumulado, cliques e visualizações diárias dos últimos 30 dias.

---

## 🛠️ Requisitos de Sistema

* **PHP 8.2 ou superior** (com extensões SQLite e GD ativas)
* **Composer**
* **Node.js & NPM** (para scripts de setup adicionais)
* **Python 3.10 ou superior** (com biblioteca `urllib3` e `beautifulsoup4` instaladas se desejar rodar o coletor de bot)

---

## ⚡ Instalação e Configuração Rápida

### 1. Preparando o Ambiente
Copie o arquivo `.env.example` para `.env`:
```bash
copy .env.example .env
```
Abra o `.env` e configure as credenciais da **Efí Bank** (Pix), as chaves **VAPID** para Web Push e o caminho do binário do Python:
```env
# Configuração do Python (ex: python ou python3)
PYTHON_BIN=python

# Chaves VAPID geradas
WEBPUSH_PUBLIC_KEY=BF1u__aGGG0-edcjpkkzvgCOAp2mL9uDgYA0FMivy4UAheMk0lSRnlE5bZ1jMa_vZrsfFCj_dlQAIB1kpz4xGQ4
WEBPUSH_PRIVATE_KEY=6udi3lpgzdsLjB1XLf_Ym-fEpW02Fk908J3EMBOH1dY
```

### 2. O Comando Mágico de Setup
O WhatsGrupos conta com um instalador wizard 100% automatizado. Basta executar o comando abaixo e responder `sim` no terminal:
```bash
php artisan whatsgrupos:setup
```
Este comando irá:
1. Criar o banco de dados local SQLite (`database/database.sqlite`)
2. Executar todas as migrações de tabelas
3. Executar todos os seeders (populando Categorias, SEO Pages, Banners e Frases)
4. Criar o link simbólico de armazenamento público (`public/storage`)
5. Verificar e testar o PATH de instalação do Python no sistema

### 3. Rodando a Aplicação
Inicie o servidor de desenvolvimento local do Laravel:
```bash
php artisan serve
```
Acesse no seu navegador: `http://localhost:8000`.

---

## 🔒 Acesso Administrativo
* Rota do Painel: `/admin/login`
* E-mail Padrão: `admin@whatsgrupos.com.br`
* Senha Padrão: `admin123`

---

## 📝 Licença
O WhatsGrupos é um software sob a licença [MIT](https://opensource.org/licenses/MIT).
