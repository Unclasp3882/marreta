FROM shinsenter/laravel:php8.4-nginx

WORKDIR /var/www/html

RUN docker-php-ext-install sockets

COPY . .

RUN composer install --no-interaction --optimize-autoloader --no-dev \
    && php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

EXPOSE 80
