# syntax=docker/dockerfile:1

# ---------------------------------------------------------------------------
# base: PHP-FPM runtime with every extension the app needs
# ---------------------------------------------------------------------------
FROM php:8.4-fpm-alpine AS base

RUN apk add --no-cache icu-libs libzip libpng libjpeg-turbo freetype libwebp libpq \
    && apk add --no-cache --virtual .build-deps $PHPIZE_DEPS icu-dev libzip-dev libpng-dev \
        libjpeg-turbo-dev freetype-dev libwebp-dev linux-headers postgresql-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j"$(nproc)" bcmath exif gd intl opcache pcntl pdo_mysql pdo_pgsql zip \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del .build-deps \
    && rm -rf /tmp/pear

WORKDIR /var/www/html

# ---------------------------------------------------------------------------
# dev: adds Composer + Node and maps www-data to the host user so files
# written from inside the container (vendor/, node_modules/, generated
# Wayfinder routes) stay owned by you on the host.
# ---------------------------------------------------------------------------
FROM base AS dev

ARG UID=1000
ARG GID=1000

RUN apk add --no-cache git unzip nodejs npm shadow \
    && groupmod -o -g "${GID}" www-data \
    && usermod -o -u "${UID}" -g www-data www-data \
    && mkdir -p /tmp/composer /tmp/npm \
    && chown -R www-data:www-data /tmp/composer /tmp/npm

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY docker/php/conf.d/dev.ini /usr/local/etc/php/conf.d/zz-app.ini

ENV COMPOSER_HOME=/tmp/composer \
    npm_config_cache=/tmp/npm

# ---------------------------------------------------------------------------
# vendor: production Composer dependencies only
# ---------------------------------------------------------------------------
FROM composer:2 AS vendor

WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --no-scripts --no-autoloader --prefer-dist --ignore-platform-reqs
COPY . .
RUN composer dump-autoload --no-dev --optimize --classmap-authoritative --no-scripts

# ---------------------------------------------------------------------------
# assets: compiled front-end. Wayfinder needs `php artisan`, so this stage
# runs on the dev image (PHP + Node) with the production vendor tree.
# ---------------------------------------------------------------------------
FROM dev AS assets

WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY . .
COPY --from=vendor /app/vendor ./vendor
RUN cp .env.example .env && php artisan key:generate --force \
    && npm run build:ssr \
    && rm .env

# ---------------------------------------------------------------------------
# prod: lean runtime image
# ---------------------------------------------------------------------------
FROM base AS prod

COPY docker/php/conf.d/prod.ini /usr/local/etc/php/conf.d/zz-app.ini

COPY --chown=www-data:www-data . .
COPY --chown=www-data:www-data --from=vendor /app/vendor ./vendor
COPY --chown=www-data:www-data --from=assets /app/public/build ./public/build

RUN rm -rf docker tests node_modules \
    && mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs \
    && chown -R www-data:www-data storage bootstrap/cache

COPY docker/php/entrypoint.sh /usr/local/bin/entrypoint
RUN chmod +x /usr/local/bin/entrypoint

USER www-data

ENTRYPOINT ["entrypoint"]
CMD ["php-fpm"]

# ---------------------------------------------------------------------------
# ssr: Node server that pre-renders Inertia pages (so search engines and link
# previews see real HTML). The bundle keeps its npm dependencies external.
# ---------------------------------------------------------------------------
FROM node:24-alpine AS ssr

WORKDIR /var/www/html
ENV NODE_ENV=production
COPY --from=assets /app/package.json ./package.json
COPY --from=assets /app/node_modules ./node_modules
COPY --from=assets /app/bootstrap/ssr ./bootstrap/ssr

USER node
EXPOSE 13714
CMD ["node", "bootstrap/ssr/app.js"]

# ---------------------------------------------------------------------------
# web: nginx serving the compiled public/ directory, proxying PHP to `app`
# ---------------------------------------------------------------------------
FROM nginx:1.27-alpine AS web

COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf
COPY public /var/www/html/public
COPY --from=assets /app/public/build /var/www/html/public/build
RUN rm -rf /var/www/html/public/storage \
    && ln -s /var/www/html/storage/app/public /var/www/html/public/storage
