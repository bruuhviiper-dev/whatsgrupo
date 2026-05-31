#!/usr/bin/env bash
# ============================================================
#  WhatsGrupo – start.sh
#  Instala dependências PHP/Python e sobe todos os serviços
#  em segundo plano sem travar o terminal.
# ============================================================

set -euo pipefail

# ── Cores ────────────────────────────────────────────────────
RED='\033[0;31m'; GREEN='\033[0;32m'; YELLOW='\033[1;33m'
CYAN='\033[0;36m'; BOLD='\033[1m'; NC='\033[0m'

ok()   { echo -e "${GREEN}[✔]${NC} $*"; }
info() { echo -e "${CYAN}[…]${NC} $*"; }
warn() { echo -e "${YELLOW}[!]${NC} $*"; }
fail() { echo -e "${RED}[✘]${NC} $*"; exit 1; }

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
LOG_DIR="$SCRIPT_DIR/storage/logs/services"
mkdir -p "$LOG_DIR"

echo -e "\n${BOLD}========================================"
echo -e "  WhatsGrupo – Inicialização completa"
echo -e "========================================${NC}\n"

# ── 1. Extensões PHP necessárias ────────────────────────────
info "Verificando extensões PHP..."

PHP_EXTS=(gd imagick pdo pdo_mysql mbstring xml zip curl fileinfo)
MISSING_PHP=()

for ext in "${PHP_EXTS[@]}"; do
    if ! php -m 2>/dev/null | grep -qi "^${ext}$"; then
        MISSING_PHP+=("php-$ext")
    fi
done

if [ ${#MISSING_PHP[@]} -gt 0 ]; then
    warn "Instalando extensões PHP ausentes: ${MISSING_PHP[*]}"
    if command -v apt-get &>/dev/null; then
        sudo apt-get update -qq
        sudo apt-get install -y -qq "${MISSING_PHP[@]}" || warn "Algumas extensões podem precisar de instalação manual."
    else
        warn "Gerenciador de pacotes não encontrado. Instale manualmente: ${MISSING_PHP[*]}"
    fi
else
    ok "Todas as extensões PHP estão presentes."
fi

# ── 2. Composer – dependências PHP ──────────────────────────
info "Instalando dependências PHP (Composer)..."
if command -v composer &>/dev/null; then
    composer install \
        --no-interaction \
        --prefer-dist \
        --optimize-autoloader \
        --no-progress \
        -d "$SCRIPT_DIR" 2>&1 | tail -5
    ok "Dependências PHP instaladas."
else
    fail "Composer não encontrado. Instale em https://getcomposer.org"
fi

# ── 3. .env ─────────────────────────────────────────────────
if [ ! -f "$SCRIPT_DIR/.env" ]; then
    info "Arquivo .env não encontrado. Copiando .env.example..."
    cp "$SCRIPT_DIR/.env.example" "$SCRIPT_DIR/.env"
    php "$SCRIPT_DIR/artisan" key:generate --ansi
    warn ".env criado – configure DB, mail e demais variáveis antes de continuar."
fi

# ── 4. Python – pip e dependências ──────────────────────────
info "Verificando Python..."
PYTHON=$(command -v python3 || command -v python || true)
[ -z "$PYTHON" ] && fail "Python 3 não encontrado. Instale antes de continuar."

PIP=$(command -v pip3 || command -v pip || true)
[ -z "$PIP" ] && fail "pip não encontrado. Instale python3-pip antes de continuar."

PYTHON_DEPS=(requests beautifulsoup4 cloudscraper lxml)

info "Instalando dependências Python: ${PYTHON_DEPS[*]}"
$PIP install --quiet --break-system-packages "${PYTHON_DEPS[@]}" 2>&1 \
    || $PIP install --quiet "${PYTHON_DEPS[@]}" 2>&1 \
    || warn "Alguns pacotes Python podem não ter instalado. Tente: pip install ${PYTHON_DEPS[*]}"
ok "Dependências Python instaladas."

# ── 5. Migrations e cache ────────────────────────────────────
info "Rodando migrations..."
php "$SCRIPT_DIR/artisan" migrate --force --no-interaction 2>&1 | tail -3
ok "Migrations concluídas."

info "Otimizando configuração..."
php "$SCRIPT_DIR/artisan" config:cache   --no-interaction 2>/dev/null || true
php "$SCRIPT_DIR/artisan" route:cache    --no-interaction 2>/dev/null || true
php "$SCRIPT_DIR/artisan" view:cache     --no-interaction 2>/dev/null || true
ok "Cache gerado."

# ── 6. Função auxiliar para subir processo em background ─────
start_bg() {
    local name="$1"; shift
    local logfile="$LOG_DIR/${name}.log"

    # Mata instância anterior se existir
    if [ -f "$LOG_DIR/${name}.pid" ]; then
        old_pid=$(cat "$LOG_DIR/${name}.pid")
        kill "$old_pid" 2>/dev/null && warn "Processo anterior de '${name}' (PID $old_pid) encerrado." || true
        rm -f "$LOG_DIR/${name}.pid"
    fi

    nohup "$@" >> "$logfile" 2>&1 &
    echo $! > "$LOG_DIR/${name}.pid"
    ok "[$name] rodando em segundo plano  |  PID: $!  |  Log: $logfile"
}

echo ""
echo -e "${BOLD}── Subindo serviços em segundo plano ──${NC}"

# ── 7. Queue Worker – fila padrão ────────────────────────────
start_bg "queue-default" \
    php "$SCRIPT_DIR/artisan" queue:work \
        --queue=default \
        --tries=3 \
        --timeout=120 \
        --sleep=3 \
        --max-time=3600

# ── 8. Queue Worker – fila 'coleta' (scraping Python) ────────
start_bg "queue-coleta" \
    php "$SCRIPT_DIR/artisan" queue:work \
        --queue=coleta \
        --tries=2 \
        --timeout=600 \
        --sleep=10 \
        --max-time=3600

# ── 9. Schedule (cron via artisan) ───────────────────────────
start_bg "scheduler" \
    bash -c 'while true; do
        php '"$SCRIPT_DIR"'/artisan schedule:run --no-interaction >> /dev/null 2>&1
        sleep 60
    done'

echo ""
echo -e "${BOLD}── Resumo dos serviços ativos ─────────${NC}"
echo -e "  ${CYAN}queue-default${NC}  →  jobs gerais (default)"
echo -e "  ${CYAN}queue-coleta${NC}   →  scraping / coleta de grupos"
echo -e "  ${CYAN}scheduler${NC}      →  cron: ExpireBoosts, Sitemap, CheckLinks, Scores"
echo ""
echo -e "  Logs em: ${YELLOW}$LOG_DIR/${NC}"
echo ""
echo -e "${GREEN}${BOLD}Tudo pronto! A aplicação está rodando.${NC}"
echo ""
echo -e "  Para parar tudo: ${YELLOW}bash $SCRIPT_DIR/stop.sh${NC}"
echo ""

# ── 10. Cria stop.sh para encerrar os serviços ───────────────
cat > "$SCRIPT_DIR/stop.sh" <<'STOP'
#!/usr/bin/env bash
LOG_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)/storage/logs/services"
for pidfile in "$LOG_DIR"/*.pid; do
    [ -f "$pidfile" ] || continue
    name=$(basename "$pidfile" .pid)
    pid=$(cat "$pidfile")
    if kill "$pid" 2>/dev/null; then
        echo "[✔] $name (PID $pid) encerrado."
    else
        echo "[!] $name (PID $pid) já estava parado."
    fi
    rm -f "$pidfile"
done
echo "Todos os serviços foram encerrados."
STOP
chmod +x "$SCRIPT_DIR/stop.sh"
