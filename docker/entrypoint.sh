#!/usr/bin/env bash
# ============================================================
#  Entrypoint de produção – roda 1x na subida do container:
#  espera o DB, migra, semeia, linka storage, gera cache,
#  dispara a coleta inicial e entrega o controle ao supervisord.
# ============================================================
set -euo pipefail

cd /var/www/html

echo "[entrypoint] Aguardando banco de dados (${DB_HOST:-mysql}:${DB_PORT:-3306})..."
ATTEMPTS=0
until php -r '
    $h=getenv("DB_HOST")?:"mysql"; $p=getenv("DB_PORT")?:"3306";
    $u=getenv("DB_USERNAME")?:"root"; $pw=getenv("DB_PASSWORD")?:"";
    $db=getenv("DB_DATABASE")?:"laravel";
    try { new PDO("mysql:host=$h;port=$p;dbname=$db", $u, $pw); exit(0); }
    catch (Throwable $e) { exit(1); }
' 2>/dev/null; do
    ATTEMPTS=$((ATTEMPTS+1))
    if [ "$ATTEMPTS" -ge 60 ]; then
        echo "[entrypoint] Banco indisponível após 60 tentativas. Abortando."
        exit 1
    fi
    sleep 2
done
echo "[entrypoint] Banco disponível."

# Garante APP_KEY
if ! grep -q "^APP_KEY=base64:" .env 2>/dev/null; then
    echo "[entrypoint] Gerando APP_KEY..."
    php artisan key:generate --force --no-interaction || true
fi

echo "[entrypoint] Migrations (--force)..."
php artisan migrate --force --no-interaction

echo "[entrypoint] Seeders idempotentes (--force)..."
php artisan db:seed --force --no-interaction || echo "[entrypoint] Seeders já aplicados."

echo "[entrypoint] Storage link..."
php artisan storage:link --no-interaction 2>/dev/null || true
mkdir -p storage/app/public/groups storage/logs/services

echo "[entrypoint] Cache de config/route/view..."
php artisan config:cache --no-interaction || true
php artisan route:cache  --no-interaction || true
php artisan view:cache   --no-interaction || true

echo "[entrypoint] Disparando coleta inicial (fila 'coleta')..."
php artisan grupos:coletar --queue --no-interaction || echo "[entrypoint] Coleta inicial não pôde ser enfileirada agora."

echo "[entrypoint] Subindo serviços via supervisord..."
exec "$@"
