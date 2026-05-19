FROM composer:2.6 AS composer-build

WORKDIR /app

COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --no-interaction \
    --no-autoloader \
    --prefer-dist \
    --ignore-platform-reqs

COPY . .

RUN rm -f bootstrap/cache/*.php

RUN DB_CONNECTION=sqlite DB_DATABASE=:memory: composer dump-autoload --optimize --verbose


FROM php:8.3-fpm-alpine AS production

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

RUN apk add --no-cache \
    nginx \
    supervisor \
    curl \
    redis \
     mysql-client
    && rm -rf /var/cache/apk/*

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

COPY docker/php/php.ini /usr/local/etc/php/php.ini
COPY docker/php/www.conf /usr/local/etc/php-fpm.d/www.conf

COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf
COPY docker/nginx/nginx.conf /etc/nginx/nginx.conf

COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

WORKDIR /var/www/html

COPY --from=composer-build /app .

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 80

CMD ["/start.sh"]