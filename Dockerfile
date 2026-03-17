# ─── Stage 1: Build dependencies ─────────────────────────────────────────────
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

# ─── Stage 2: Final image ─────────────────────────────────────────────────────
FROM php:8.2-fpm-alpine

# Install system dependencies + PHP extensions in ONE layer
RUN apk add --no-cache \
        nginx \
        supervisor \
        mysql-client \
        nodejs \
        npm \
        curl \
        libpng-dev \
        libzip-dev \
        zip \
        unzip \
        oniguruma-dev \
    && docker-php-ext-install \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
    && rm -rf /var/cache/apk/*

# Copy vendor from build stage
COPY --from=vendor /app/vendor /var/www/html/vendor

# Copy application files
COPY . /var/www/html

WORKDIR /var/www/html

# Set permissions in ONE layer
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache \
    && mkdir -p /var/www/html/storage/app/public/attachments \
    && mkdir -p /var/www/html/storage/framework/{cache,sessions,views} \
    && mkdir -p /var/www/html/storage/logs

# Copy config files
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/entrypoint.sh /entrypoint.sh

RUN chmod +x /entrypoint.sh

EXPOSE 80

ENTRYPOINT ["/entrypoint.sh"]
