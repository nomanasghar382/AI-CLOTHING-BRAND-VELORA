FROM php:8.3-fpm-alpine

RUN apk add --no-cache bash git curl libpng-dev oniguruma-dev libxml2-dev zip unzip mysql-client \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY backend/ /var/www/html/
RUN composer install --no-dev --optimize-autoloader --no-interaction

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

USER www-data
EXPOSE 9000
CMD ["php-fpm"]
