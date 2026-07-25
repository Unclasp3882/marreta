# syntax=docker/dockerfile:1

FROM serversideup/php:8.4-fpm-nginx AS base

USER root

RUN install-php-extensions intl sockets

COPY --chmod=755 docker/entrypoint.d/ /etc/entrypoint.d/

USER www-data

FROM base AS production

ENV HEALTHCHECK_PATH="/up" \
    LOG_CHANNEL="stderr" \
    PHP_OPCACHE_ENABLE="1" \
    AUTORUN_ENABLED="true" \
    AUTORUN_LARAVEL_MIGRATION_SEED="true" \
    AUTORUN_LARAVEL_STORAGE_LINK="false"

# Install dependencies first so the Composer layer survives application changes.
COPY --chown=www-data:www-data composer.json composer.lock ./

RUN composer install \
        --no-dev \
        --no-interaction \
        --no-progress \
        --no-scripts \
        --no-autoloader \
        --prefer-dist

COPY --chown=www-data:www-data . .

RUN mkdir -p \
        bootstrap/cache \
        storage/app/marreta-cache \
        storage/framework/cache/data \
        storage/framework/sessions \
        storage/framework/views \
        storage/logs && \
    \
    composer dump-autoload --no-dev --optimize --no-interaction
