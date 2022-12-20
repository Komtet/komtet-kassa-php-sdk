FROM php:5.6-cli as php5
WORKDIR /home/php_sdk

ENV COMPOSER_ALLOW_SUPERUSER 1

RUN apt-get update && apt-get install -y git && \
    apt-get install -y zip && apt-get install -y ca-certificates

COPY ./ .

RUN curl -s https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer && \
    composer require phpunit/phpunit --dev && \
    composer self-update --snapshot && \
    composer install

FROM php:7.2-cli as php7
WORKDIR /home/php_sdk

ENV COMPOSER_ALLOW_SUPERUSER 1

RUN apt-get update && apt-get install -y git && \
    apt-get install -y zip

COPY ./ .

RUN curl -s https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer && \
    composer require phpunit/phpunit ^4.8 --dev && \
    composer self-update --snapshot && \
    composer install
