# ============================================================
#  WhatsGrupo – Imagem de produção (all-in-one)
#  PHP 8.2-FPM + Nginx + Python 3 (coletor) + Supervisor
#  Sobe site + filas + scheduler + coleta com UM comando.
# ============================================================
FROM php:8.2-fpm-bullseye

ENV DEBIAN_FRONTEND=noninteractive \
    COMPOSER_ALLOW_SUPERUSER=1 \
    PYTHON_BIN=/var/www/html/.venv/bin/python

# ── Dependências de sistema + libs para GD/WebP + Python + Nginx + Supervisor ──
RUN apt-get update && apt-get install -y --no-install-recommends \
        nginx supervisor \
        python3 python3-pip python3-venv \
        git unzip zip curl \
        libpng-dev libjpeg62-turbo-dev libwebp-dev libfreetype6-dev \
        libonig-dev libzip-dev libxml2-dev \
        default-mysql-client \
    && rm -rf /var/lib/apt/lists/*

# ── Extensões PHP (GD com WebP, pdo_mysql, mbstring, zip, bcmath, exif, pcntl, opcache) ──
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j"$(nproc)" \
        gd pdo_mysql mbstring zip bcmath exif pcntl opcache

# ── Composer ──
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# ── Dependências PHP (camada cacheável) ──
COPY composer.json composer.lock ./
RUN composer install --no-interaction --prefer-dist --no-progress \
        --no-scripts --no-autoloader --no-dev

# ── Código da aplicação ──
COPY . .
RUN composer dump-autoload --optimize --no-dev \
    && composer run-script post-autoload-dump --no-interaction || true

# ── Ambiente virtual Python + dependências do coletor ──
RUN python3 -m venv /var/www/html/.venv \
    && /var/www/html/.venv/bin/pip install --no-cache-dir --upgrade pip \
    && /var/www/html/.venv/bin/pip install --no-cache-dir \
        requests beautifulsoup4 cloudscraper lxml nudenet \
    # Pré-aquece o modelo nudenet (baixa do HuggingFace durante o build).
    # Garante que a análise NSFW funcione offline em produção.
    && /var/www/html/.venv/bin/python -c \
        "from nudenet import NudeDetector; NudeDetector(); print('[nudenet] modelo pré-carregado')" \
        2>/dev/null || true

# ── Permissões ──
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# ── Configs de Nginx / Supervisor / Entrypoint ──
COPY docker/nginx.conf       /etc/nginx/sites-available/default
COPY docker/supervisord.conf /etc/supervisor/supervisord.conf
COPY docker/entrypoint.sh    /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/supervisord.conf"]
