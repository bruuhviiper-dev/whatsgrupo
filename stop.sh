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
