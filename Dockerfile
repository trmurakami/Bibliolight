FROM php:7.4-fpm
WORKDIR /var/www/html
RUN apt-get update && \
    apt-get upgrade -y && \
    apt-get install -y git zip unzip libzip4

COPY ./ ./
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer && composer install --no-dev --no-interaction 




