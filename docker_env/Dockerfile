FROM php:7.4-cli AS php7
WORKDIR /home/php_sdk

ENV COMPOSER_ALLOW_SUPERUSER 1

RUN apt-get update && apt-get install -y git zip unzip ca-certificates

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

COPY ./ .

RUN composer require phpunit/phpunit --dev && \
    composer install

FROM php:8.2-cli AS php8
WORKDIR /home/php_sdk

ENV COMPOSER_ALLOW_SUPERUSER 1

RUN apt-get update && apt-get install -y git zip unzip ca-certificates

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

COPY ./ .

RUN composer require phpunit/phpunit ^9.6 --dev && \
    composer install
