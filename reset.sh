#!/usr/bin/env bash
# ============================================================
#  WhatsGrupo – reset.sh
#  Para TODOS os serviços, mata workers órfãos, limpa PIDs e
#  caches do Laravel para que o start.sh possa rodar do zero.
#
#  Uso:
#    bash reset.sh            → para serviços + limpa cache e
#                               PERGUNTA se deseja apagar o banco
#    bash reset.sh --hard     → também derruba a fila (queue:clear)
#    bash reset.sh --db       → apaga o banco SEM perguntar (db:wipe)
#    bash reset.sh --all      → --hard + --db (reset TOTAL, sem perguntar)
#    bash reset.sh --keep-db  → nunca apaga o banco (não pergunta)
#
#  O banco é apagado com `db:wipe`, que afeta APENAS o schema
#  conectado (DB_DATABASE no .env). Depois rode `bash start.sh`
#  para recriar migrations + seeders do zero.
# ============================================================

set -uo pipefail

GREEN='\033[0;32m'; YELLOW='\033[1;33m'; CYAN='\033[0;36m'; RED='\033[0;31m'; NC='\033[0m'
ok()   { echo -e "${GREEN}[✔]${NC} $*"; }
info() { echo -e "${CYAN}[…]${NC} $*"; }
warn() { echo -e "${YELLOW}[!]${NC} $*"; }
err()  { echo -e "${RED}[✘]${NC} $*"; }

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
LOG_DIR="$SCRIPT_DIR/storage/logs/services"

# ── Parsing de argumentos ───────────────────────────────────
HARD=0; RESET_DB=0; KEEP_DB=0; ASK_DB=1
for arg in "$@"; do
    case "$arg" in
        --hard)    HARD=1 ;;
        --db)      RESET_DB=1; ASK_DB=0 ;;
        --all)     HARD=1; RESET_DB=1; ASK_DB=0 ;;
        --keep-db) KEEP_DB=1; ASK_DB=0 ;;
        *)         warn "Argumento desconhecido: $arg" ;;
    esac
done

info "Parando serviços via PIDs registrados..."
if [ -d "$LOG_DIR" ]; then
    for pidfile in "$LOG_DIR"/*.pid; do
        [ -f "$pidfile" ] || continue
        name=$(basename "$pidfile" .pid)
        pid=$(cat "$pidfile" 2>/dev/null || echo "")
        if [ -n "$pid" ] && kill "$pid" 2>/dev/null; then
            ok "$name (PID $pid) encerrado."
        else
            warn "$name (PID $pid) já estava parado."
        fi
        rm -f "$pidfile"
    done
else
    warn "Nenhum diretório de PIDs encontrado ($LOG_DIR)."
fi

# Mata workers/scheduler remanescentes (best-effort, multiplataforma)
info "Encerrando workers/scheduler remanescentes..."
pkill -f "artisan queue:work"   2>/dev/null && ok "queue:work encerrados."  || true
pkill -f "artisan queue:listen" 2>/dev/null || true
pkill -f "artisan schedule:run"  2>/dev/null || true
pkill -f "artisan schedule:work" 2>/dev/null && ok "scheduler encerrado." || true

# Limpa caches do Laravel (config/route/view) para evitar estado preso
info "Limpando caches do Laravel..."
php "$SCRIPT_DIR/artisan" config:clear --no-interaction 2>/dev/null || true
php "$SCRIPT_DIR/artisan" route:clear  --no-interaction 2>/dev/null || true
php "$SCRIPT_DIR/artisan" view:clear   --no-interaction 2>/dev/null || true
php "$SCRIPT_DIR/artisan" cache:clear  --no-interaction 2>/dev/null || true
ok "Caches limpos."

if [ "$HARD" -eq 1 ]; then
    warn "Modo --hard: limpando a fila (jobs pendentes) e jobs falhos..."
    php "$SCRIPT_DIR/artisan" queue:clear --no-interaction 2>/dev/null || true
    php "$SCRIPT_DIR/artisan" queue:flush --no-interaction 2>/dev/null || true
    ok "Fila limpa."
fi

# ── Reset do banco de dados (destrutivo) ────────────────────
if [ "$KEEP_DB" -eq 0 ]; then
    # Pergunta interativa só se não foi passado --db/--all/--keep-db
    # e se há um terminal interativo (TTY) para ler a resposta.
    if [ "$ASK_DB" -eq 1 ]; then
        if [ -t 0 ]; then
            echo ""
            warn "Deseja APAGAR TODO o banco de dados (db:wipe no schema do .env)?"
            warn "Isso remove TODAS as tabelas. O start.sh recriará tudo do zero."
            printf "Digite 'ok' (ou 'sim') para confirmar: "
            read -r resposta
            case "${resposta,,}" in
                ok|sim|s|yes|y) RESET_DB=1 ;;
                *) info "Banco preservado (resposta diferente de 'ok')." ;;
            esac
        else
            info "Sem terminal interativo — banco preservado. Use --db para forçar."
        fi
    fi

    if [ "$RESET_DB" -eq 1 ]; then
        warn "Apagando todas as tabelas do banco (db:wipe)..."
        if php "$SCRIPT_DIR/artisan" db:wipe --force --no-interaction; then
            ok "Banco zerado. Rode 'bash start.sh' para recriar migrations + seeders."
        else
            err "Falha ao executar db:wipe. Verifique a conexão do banco no .env."
        fi
    fi
fi

echo ""
ok "Reset concluído. Agora rode: ${YELLOW}bash start.sh${NC}"
