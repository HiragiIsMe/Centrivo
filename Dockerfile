# ==============================================================================
# STAGE 1: COMPOSER BACKEND BUILD
# ==============================================================================
FROM composer:2.6 AS composer-build

WORKDIR /app

# Salin file konfigurasi dependensi composer
COPY composer.json composer.lock ./

# Install library produksi tanpa menyertakan package testing/development
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-autoloader \
    --prefer-dist \
    --ignore-platform-reqs

# Salin seluruh source code backend Laravel
COPY . .

# Bersihkan sisa-sisa cache bootstrap bawaan lokal laptop
RUN rm -f bootstrap/cache/*.php

# Optimasi autoloader dengan membungkam pencarian database asli menggunakan SQLite memori bayangan
RUN DB_CONNECTION=sqlite DB_DATABASE=:memory: composer dump-autoload --optimize --verbose


# ==============================================================================
# STAGE 2: PRODUCTION RUNTIME ENVIRONMENT
# ==============================================================================
FROM php:8.3-fpm-alpine AS production

# Ambil script installer ekstensi otomatis resmi agar kompilasi di Alpine stabil dan ringan
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

# Install package sistem operasi dasar yang dibutuhkan aplikasi untuk berjalan
RUN apk add --no-cache \
    nginx \
    supervisor \
    curl \
    redis \
    && rm -rf /var/cache/apk/*

# Install seluruh ekstensi PHP inti beserta Redis (Otomatis mendownload dependency & membersihkannya kembali)
RUN install-php-extensions \
    pdo \
    pdo_mysql \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    intl \
    opcache \
    redis

# Salin seluruh konfigurasi server (PHP, Nginx, Supervisor) dari folder docker project ke dalam image
COPY docker/php/php.ini /usr/local/etc/php/php.ini
COPY docker/php/www.conf /usr/local/etc/php-fpm.d/www.conf

COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf
COPY docker/nginx/nginx.conf /etc/nginx/nginx.conf

COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Tentukan folder kerja utama di dalam container
WORKDIR /var/www/html

# Ambil hasil masakan source code bersih beserta folder vendor dari STAGE 1
COPY --from=composer-build /app .

# Atur kepemilikan dan hak akses tulis agar Laravel bisa memproses log, session, dan cache tanpa hambatan
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# Siapkan script otomatisator startup container
COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

# Buka akses port 80 untuk lalu lintas web
EXPOSE 80

# Jalankan server via script startup
CMD ["/start.sh"]