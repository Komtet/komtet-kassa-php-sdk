FROM php:5.4-cli
WORKDIR /home/php_sdk

ENV COMPOSER_ALLOW_SUPERUSER 1

RUN apt-get update && apt-get install -y git && \
    apt-get install -y zip

COPY ./ .

RUN curl -s https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer && \
    composer require phpunit/phpunit --dev && \
    composer self-update --snapshot && \
    composer install
