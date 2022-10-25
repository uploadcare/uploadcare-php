FROM composer:latest as composer
FROM php:7.4-fpm-alpine as php

RUN apk add --no-cache git

RUN set -xe \
    && apk add --no-cache --virtual .phpize-deps $PHPIZE_DEPS \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && apk del --no-network .phpize-deps

COPY --from=composer /usr/bin/composer /usr/local/bin/composer
WORKDIR /var/www/app
