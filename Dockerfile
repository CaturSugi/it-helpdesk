# ── Stage 1: Composer dependencies ────────────────────────────────────────────
FROM composer:2.6 AS vendor

WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-progress \
    --no-scripts \
    --prefer-dist \
    --optimize-autoloader \
    --ignore-platform-reqs

# ── Stage 2: Production image ─────────────────────────────────────────────────
FROM php:8.2-fpm-alpine

# Install all deps in single layer
RUN apk add --no-cache \
        nginx \
        supervisor \
        mysql-client \
        curl \
        libpng-dev \
        libzip-dev \
        zip \
        unzip \
        oniguruma-dev \
        libxml2-dev \
    && docker-php-ext-install \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        xml \
    && mkdir -p /var/log/supervisor /var/log/nginx /run/nginx \
    && rm -rf /var/cache/apk/* /tmp/*

WORKDIR /var/www/html

# Copy vendor from composer stage
COPY --from=vendor /app/vendor ./vendor

# Copy app source
COPY . .

# Setup directories & permissions
RUN mkdir -p \
        storage/app/public/attachments \
        storage/framework/cache/data \
        storage/framework/sessions \
        storage/framework/views \
        storage/logs \
        bootstrap/cache \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# Copy Docker configs
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 80

ENTRYPOINT ["/entrypoint.sh"]
