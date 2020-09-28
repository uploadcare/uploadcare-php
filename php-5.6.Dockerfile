FROM php:5.6-fpm-alpine as php

RUN apk add --no-cache git

RUN set -xe \
    && apk add --no-cache --virtual .phpize-deps $PHPIZE_DEPS \
    && pecl install xdebug-2.5.5 \
    && docker-php-ext-enable xdebug \
    && apk del --no-network .phpize-deps

RUN curl -sS https://getcomposer.org/installer | php -- \
        --install-dir=/usr/local/bin \
        --filename=composer

