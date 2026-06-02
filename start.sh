#!/usr/bin/env bash
# ============================================================
#  WhatsGrupo – start.sh  v4.0
#  Instala TODAS as dependências, roda migrations + seeders
#  e sobe todos os serviços em segundo plano.
#
#  Execute UMA VEZ no servidor (ou quando atualizar).
#  Suporte: Linux, macOS, Windows (Git Bash / MINGW64 / WSL)
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

# ── Detectar sistema operacional ────────────────────────────
detect_os() {
    case "$(uname -s)" in
        Linux*)   echo "linux"   ;;
        Darwin*)  echo "mac"     ;;
        CYGWIN*)  echo "windows" ;;
        MINGW*)   echo "windows" ;;
        MSYS*)    echo "windows" ;;
        *)        echo "unknown" ;;
    esac
}
OS=$(detect_os)
info "Sistema detectado: ${BOLD}$OS${NC}"

# ── 1. Extensões PHP necessárias ────────────────────────────
info "Verificando extensões PHP..."

PHP_EXTS=(gd pdo pdo_mysql mbstring xml zip curl fileinfo json openssl tokenizer)
MISSING_PHP=()

for ext in "${PHP_EXTS[@]}"; do
    if ! php -m 2>/dev/null | grep -qi "^${ext}$"; then
        MISSING_PHP+=("php-$ext")
    fi
done

if [ ${#MISSING_PHP[@]} -gt 0 ]; then
    warn "Instalando extensões PHP ausentes: ${MISSING_PHP[*]}"
    if [ "$OS" = "linux" ] && command -v apt-get &>/dev/null; then
        sudo apt-get update -qq
        sudo apt-get install -y -qq "${MISSING_PHP[@]}" || warn "Algumas extensões podem precisar de instalação manual."
    elif [ "$OS" = "mac" ] && command -v brew &>/dev/null; then
        brew install "${MISSING_PHP[@]}" 2>/dev/null || warn "Verifique as extensões manualmente."
    else
        warn "Instale manualmente as extensões PHP: ${MISSING_PHP[*]}"
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

# ── 4. Python – instalação automática ───────────────────────
PYTHON_DEPS=(requests beautifulsoup4 cloudscraper lxml)

install_python_windows() {
    info "Windows detectado – verificando Python no PATH do sistema..."
    WIN_PYTHON=$(cmd.exe /c "where python 2>nul" 2>/dev/null | head -1 | tr -d '\r' || true)
    if [ -n "$WIN_PYTHON" ]; then
        PYTHON=$(cygpath -u "$WIN_PYTHON" 2>/dev/null || echo "$WIN_PYTHON")
        ok "Python encontrado via Windows PATH: $PYTHON"
        return 0
    fi
    for ver in "313" "312" "311" "310" "39"; do
        for base in "/c/Python${ver}" "/c/Users/$USERNAME/AppData/Local/Programs/Python/Python${ver}"; do
            if [ -f "${base}/python.exe" ]; then
                PYTHON="${base}/python.exe"
                ok "Python encontrado em: $PYTHON"
                return 0
            fi
        done
    done
    warn "Python não encontrado automaticamente no Windows."
    if cmd.exe /c "winget --version" &>/dev/null 2>&1; then
        warn "Tentando instalar Python via winget automaticamente..."
        cmd.exe /c "winget install --id Python.Python.3.11 --accept-source-agreements --accept-package-agreements" 2>/dev/null && {
            ok "Python instalado via winget! Reinicie o terminal e execute start.sh novamente."
            exit 0
        } || warn "winget não conseguiu instalar. Instale manualmente."
    fi
    fail "Python é necessário. Instale em https://python.org e execute start.sh novamente."
}

install_python_linux() {
    info "Tentando instalar Python via pyenv..."
    PYENV_DIR="$HOME/.pyenv"
    if [ ! -d "$PYENV_DIR" ]; then
        curl -fsSL https://pyenv.run | bash 2>&1 | tail -5 || true
    fi
    if [ -d "$PYENV_DIR" ]; then
        export PYENV_ROOT="$PYENV_DIR"
        export PATH="$PYENV_ROOT/bin:$PATH"
        eval "$(pyenv init -)" 2>/dev/null || true
        pyenv install -s "3.11.9" && pyenv global "3.11.9"
        PYTHON=$(command -v python3 || command -v python)
        ok "Python instalado via pyenv."
        return 0
    fi
    if command -v apt-get &>/dev/null; then
        sudo apt-get update -qq
        sudo apt-get install -y -qq python3 python3-pip python3-venv || true
    elif command -v yum &>/dev/null; then
        sudo yum install -y python3 python3-pip || true
    elif command -v dnf &>/dev/null; then
        sudo dnf install -y python3 python3-pip || true
    fi
}

install_python_mac() {
    if command -v brew &>/dev/null; then
        brew install python@3.11 || true
        brew link python@3.11 --force 2>/dev/null || true
    else
        fail "Homebrew não encontrado. Instale Python em https://python.org"
    fi
}

setup_python_deps() {
    local python_bin="$1"
    VENV_DIR="$SCRIPT_DIR/.venv"

    if [ ! -d "$VENV_DIR" ]; then
        info "Criando ambiente virtual Python em .venv ..."
        "$python_bin" -m venv "$VENV_DIR" 2>/dev/null || {
            warn "venv não disponível. Instalando dependências Python globalmente..."
            "$python_bin" -m pip install --quiet --break-system-packages "${PYTHON_DEPS[@]}" 2>/dev/null \
                || "$python_bin" -m pip install --quiet "${PYTHON_DEPS[@]}" 2>/dev/null \
                || warn "Instale manualmente: pip install ${PYTHON_DEPS[*]}"
            return 0
        }
    fi

    # Ativa venv e instala dependências
    source "$VENV_DIR/bin/activate" 2>/dev/null || source "$VENV_DIR/Scripts/activate" 2>/dev/null || true
    pip install --quiet --upgrade pip 2>/dev/null || true
    pip install --quiet "${PYTHON_DEPS[@]}" 2>/dev/null || warn "Alguns pacotes Python podem não ter instalado."
    deactivate 2>/dev/null || true

    ok "Dependências Python instaladas no venv: $VENV_DIR"

    # Determina o caminho do Python no venv
    if [ "$OS" = "windows" ]; then
        PYTHON_VENV="$VENV_DIR/Scripts/python"
    else
        PYTHON_VENV="$VENV_DIR/bin/python"
    fi

    # Atualiza PYTHON_BIN no .env para que o Laravel use o venv
    if [ -f "$SCRIPT_DIR/.env" ]; then
        if grep -q "^PYTHON_BIN=" "$SCRIPT_DIR/.env"; then
            sed -i "s|^PYTHON_BIN=.*|PYTHON_BIN=${PYTHON_VENV}|" "$SCRIPT_DIR/.env"
        else
            echo "PYTHON_BIN=${PYTHON_VENV}" >> "$SCRIPT_DIR/.env"
        fi
        ok "PYTHON_BIN atualizado no .env: ${PYTHON_VENV}"
    fi
}

# ── Fluxo principal de instalação do Python ─────────────────
info "Verificando Python..."
PYTHON=$(command -v python3 2>/dev/null || command -v python 2>/dev/null || true)

if [ -z "$PYTHON" ] || ! "$PYTHON" --version &>/dev/null 2>&1; then
    warn "Python não encontrado no PATH. Iniciando instalação automática..."
    case "$OS" in
        windows) install_python_windows ;;
        linux)   install_python_linux   ;;
        mac)     install_python_mac     ;;
        *)       fail "Sistema não suportado. Instale Python manualmente: https://python.org" ;;
    esac
    PYTHON=$(command -v python3 2>/dev/null || command -v python 2>/dev/null || true)
    [ -z "$PYTHON" ] && fail "Python ainda não encontrado após tentativa de instalação."
fi

PYTHON_VER=$("$PYTHON" --version 2>&1)
ok "Python encontrado: $PYTHON_VER"

setup_python_deps "$PYTHON"

# ── 5. Storage link ──────────────────────────────────────────
info "Verificando link simbólico de storage..."
if [ ! -L "$SCRIPT_DIR/public/storage" ] && [ ! -d "$SCRIPT_DIR/public/storage" ]; then
    php "$SCRIPT_DIR/artisan" storage:link --no-interaction 2>/dev/null || warn "Não foi possível criar o storage link."
    ok "Storage link criado."
else
    ok "Storage link já existe."
fi
mkdir -p "$SCRIPT_DIR/storage/app/public/groups"

# ── 6. Migrations ────────────────────────────────────────────
info "Rodando migrations (--force)..."
# Executa em ordem correta — o Laravel já usa os timestamps dos arquivos
php "$SCRIPT_DIR/artisan" migrate --force --no-interaction 2>&1 | tail -10
ok "Migrations concluídas."

# ── 7. Seeders ───────────────────────────────────────────────
info "Rodando seeders (--force)..."
# Seeders são idempotentes (usam firstOrCreate/updateOrCreate)
php "$SCRIPT_DIR/artisan" db:seed --force --no-interaction 2>&1 | tail -10
ok "Seeders concluídos."

# ── 8. Cache de configuração ─────────────────────────────────
info "Limpando e otimizando cache..."
php "$SCRIPT_DIR/artisan" config:clear   --no-interaction 2>/dev/null || true
php "$SCRIPT_DIR/artisan" route:clear    --no-interaction 2>/dev/null || true
php "$SCRIPT_DIR/artisan" view:clear     --no-interaction 2>/dev/null || true
php "$SCRIPT_DIR/artisan" config:cache   --no-interaction 2>/dev/null || true
php "$SCRIPT_DIR/artisan" route:cache    --no-interaction 2>/dev/null || true
php "$SCRIPT_DIR/artisan" view:cache     --no-interaction 2>/dev/null || true
ok "Cache gerado."

# ── 9. Função auxiliar para subir processo em background ─────
start_bg() {
    local name="$1"; shift
    local logfile="$LOG_DIR/${name}.log"

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

# ── 10. Queue Worker – fila padrão ───────────────────────────
start_bg "queue-default" \
    php "$SCRIPT_DIR/artisan" queue:work \
        --queue=default \
        --tries=3 \
        --timeout=120 \
        --sleep=3 \
        --max-time=3600

# ── 11. Queue Worker – fila 'coleta' (scraping Python) ───────
start_bg "queue-coleta" \
    php "$SCRIPT_DIR/artisan" queue:work \
        --queue=coleta \
        --tries=2 \
        --timeout=600 \
        --sleep=10 \
        --max-time=3600

# ── 12. Schedule (cron via artisan) ──────────────────────────
start_bg "scheduler" \
    bash -c 'while true; do
        php '"$SCRIPT_DIR"'/artisan schedule:run --no-interaction >> /dev/null 2>&1
        sleep 60
    done'

# ── 12.5. Coleta inicial de grupos (dispara 1x na fila 'coleta') ─
# Enfileira CollectGroupsJob na fila 'coleta'; o worker 'queue-coleta'
# (subido acima) processa em segundo plano sem travar o boot.
info "Disparando coleta inicial de grupos (fila 'coleta')..."
php "$SCRIPT_DIR/artisan" grupos:coletar --queue --no-interaction 2>&1 | tail -3 \
    || warn "Não foi possível disparar a coleta inicial agora (verifique o log)."
ok "Coleta inicial enfileirada — será processada pelo worker 'queue-coleta'."

echo ""
echo -e "${BOLD}── Resumo dos serviços ativos ─────────${NC}"
echo -e "  ${CYAN}queue-default${NC}  →  jobs gerais (default)"
echo -e "  ${CYAN}queue-coleta${NC}   →  scraping / coleta de grupos"
echo -e "  ${CYAN}scheduler${NC}      →  cron: ExpireBoosts, Sitemap, CheckLinks, Scores"
echo ""
echo -e "  Logs em: ${YELLOW}$LOG_DIR/${NC}"

if [ -d "$SCRIPT_DIR/.venv" ]; then
    echo ""
    echo -e "  ${CYAN}Python venv:${NC} $SCRIPT_DIR/.venv"
    echo -e "  Para usar manualmente:"
    if [ "$OS" = "windows" ]; then
        echo -e "    ${YELLOW}source .venv/Scripts/activate${NC}   (Git Bash)"
        echo -e "    ${YELLOW}.venv\\Scripts\\activate${NC}          (CMD/PowerShell)"
    else
        echo -e "    ${YELLOW}source .venv/bin/activate${NC}"
    fi
fi

echo ""
echo -e "${GREEN}${BOLD}Tudo pronto! A aplicação está rodando.${NC}"
echo ""
echo -e "  Para parar tudo: ${YELLOW}bash $SCRIPT_DIR/stop.sh${NC}"
echo ""

# ── 13. Recria stop.sh para encerrar os serviços ─────────────
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
