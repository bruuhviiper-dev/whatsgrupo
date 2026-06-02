#!/usr/bin/env bash
# ============================================================
#  WhatsGrupo – reset.sh
#  Para TODOS os serviços, mata workers órfãos, limpa PIDs e
#  caches do Laravel para que o start.sh possa rodar do zero.
#
#  Uso:
#    bash reset.sh           → reset padrão (para serviços + limpa cache)
#    bash reset.sh --hard    → também derruba a fila (queue:clear) e PIDs
# ============================================================

set -uo pipefail

GREEN='\033[0;32m'; YELLOW='\033[1;33m'; CYAN='\033[0;36m'; NC='\033[0m'
ok()   { echo -e "${GREEN}[✔]${NC} $*"; }
info() { echo -e "${CYAN}[…]${NC} $*"; }
warn() { echo -e "${YELLOW}[!]${NC} $*"; }

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
LOG_DIR="$SCRIPT_DIR/storage/logs/services"
HARD=0
[ "${1:-}" = "--hard" ] && HARD=1

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
pkill -f "artisan queue:work"  2>/dev/null && ok "queue:work encerrados."  || true
pkill -f "artisan queue:listen" 2>/dev/null || true
pkill -f "artisan schedule:run" 2>/dev/null || true
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
    php "$SCRIPT_DIR/artisan" queue:clear        --no-interaction 2>/dev/null || true
    php "$SCRIPT_DIR/artisan" queue:flush         --no-interaction 2>/dev/null || true
    ok "Fila limpa."
fi

echo ""
ok "Reset concluído. Agora rode: ${YELLOW}bash start.sh${NC}"
