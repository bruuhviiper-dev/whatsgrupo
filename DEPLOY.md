# Deploy WhatsGrupo — um único comando

Todo o sistema (site + filas + scheduler + coleta de grupos) sobe com **um comando**.
Há dois caminhos: **Docker** (recomendado para servidor) e **bare-metal** (`start.sh`).

---

## Opção A — Docker (recomendado)

Pré-requisitos: Docker + Docker Compose no servidor.

```bash
# 1. Configure o .env (copie de .env.example e ajuste DB/keys/pagamentos)
cp .env.example .env

# 2. Suba tudo (build da imagem + MySQL + app)
docker compose up -d --build
```

O `entrypoint.sh` roda automaticamente, **1x na subida**:
1. Espera o MySQL ficar pronto;
2. `migrate --force` (em ordem) e `db:seed --force` (idempotente);
3. `storage:link` + cria `storage/app/public/groups`;
4. `config:cache` / `route:cache` / `view:cache`;
5. Dispara a **coleta inicial** na fila `coleta`;
6. Entrega ao **supervisord**, que mantém vivos:
   - `php-fpm` + `nginx` (site na porta `${APP_PORT:-8080}`);
   - `queue-default` (jobs gerais);
   - `queue-coleta` (scraping Python, jobs longos);
   - `scheduler` (`schedule:work` → ExpireBoosts a cada 1 min, coleta semanal, sitemap, etc.).

Workers e scheduler **reiniciam sozinhos** se caírem (`autorestart=true`).

Logs: `docker compose logs -f app` e `storage/logs/services/*.log`, coleta em `storage/logs/mineracao.log`.

---

## Opção B — Bare-metal (`start.sh`)

Para Linux/macOS (e Git Bash no Windows). Requer `bash`, `php`, `composer` e `python3` no PATH.

```bash
bash start.sh
```

Faz tudo de uma vez: instala deps PHP (Composer) e Python (venv com `beautifulsoup4`,
`cloudscraper`, `lxml`), roda migrations + seeders, gera cache, sobe os workers
(`queue-default`, `queue-coleta`) e o `scheduler` em segundo plano, e **dispara a coleta inicial**.

Parar tudo:
```bash
bash stop.sh
```

Resetar (parar serviços + matar workers órfãos + limpar caches) antes de subir de novo:
```bash
bash reset.sh           # reset padrão
bash reset.sh --hard    # também limpa a fila (queue:clear)
```

Em produção, rode o `start.sh` sob um supervisor do SO (systemd/supervisord) ou use a Opção A.

---

## Tuning opcional do coletor (variáveis de ambiente)

Por padrão o coletor varre **tudo** (16 diretórios + buscadores por categoria).
Para testes rápidos ou limitar carga, defina antes de rodar:

| Variável                 | Efeito                                              | Default        |
|--------------------------|-----------------------------------------------------|----------------|
| `COLLECTOR_MAX_DIRS`     | Máximo de diretórios da Fase 1                      | todos (16)     |
| `COLLECTOR_MAX_PAGES`    | Máximo de páginas por diretório                     | o de cada site |
| `COLLECTOR_MAX_GROUPS`   | Teto de grupos coletados (para early)               | sem teto       |
| `COLLECTOR_SKIP_SEARCH`  | `1` pula a Fase 2 (buscadores DuckDuckGo/Bing)      | desativado     |

Exemplo (coleta rápida só dos buscadores, até 5 grupos):
```bash
COLLECTOR_MAX_DIRS=0 COLLECTOR_MAX_GROUPS=5 php artisan grupos:coletar
```

---

## Regras de negócio garantidas na coleta

- **Unicidade por hash** imune a variações de URL (`/invite/`, `/join/`, `/v/`, `/v=`, hash direta);
  canais usam prefixo `channel_` — paridade total com o cadastro manual.
- **Conversão de imagem para WebP** (Intervention v2 → GD → Imagick → fallback original).
- **Categoria inexistente → `Outros`** (nunca perde o grupo).
- **3 regras fixas** obrigatórias e detecção automática de **apostas** (`is_gambling`, não impulsionável).
- Grupos entram como **`pending`** (moderação) com `submitter_email = bot@whatsgrupos.com`.
