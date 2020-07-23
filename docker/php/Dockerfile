FROM php:7.4-fpm-alpine3.11 as basic
RUN apk add --no-cache \
        bash \
        libzip-dev \
        openssl-dev \
        rabbitmq-c-dev && \
    docker-php-ext-install -j$(nproc) \
        zip && \
    apk add --no-cache --virtual .phpize_deps $PHPIZE_DEPS && \
    pecl install \
        amqp-1.9.4 && \
    docker-php-ext-enable \
        amqp && \
    rm -rf /usr/share/php7 && \
    rm -rf /tmp/pear && \
    apk del .phpize_deps && \
    mkdir /var/app

RUN docker-php-ext-install sockets

COPY docker/php/config/php_prod.ini /usr/local/etc/php/php.ini
ENV PATH /var/app/bin:/var/app/vendor/bin:$PATH
WORKDIR /var/app

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer