FROM serversideup/php:8.5-fpm-nginx

USER root

RUN install-php-extensions pdo_pgsql pgsql

WORKDIR /var/www/html

COPY --chown=www-data:www-data . /var/www/html

USER www-data

RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist
