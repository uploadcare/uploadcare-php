FROM php:5.5.37-fpm-alpine

RUN apk add --no-cache git && docker-php-ext-install curl

RUN docker-php-source extract && apk add --no-cache $PHPIZE_DEPS
RUN cd /usr/local/ && curl https://xdebug.org/files/xdebug-2.2.6.tgz -o xdebug-2.2.6.tgz && \
    tar -xzf xdebug-2.2.6.tgz && cd xdebug-2.2.6/ && phpize && ./configure --enable-xdebug && \
    make && make install && \
    echo "zend_extension=xdebug.so" >> /usr/local/etc/php/conf.d/xdebug.ini

RUN curl -sS https://getcomposer.org/installer | php -- \
        --install-dir=/usr/local/bin \
        --filename=composer
