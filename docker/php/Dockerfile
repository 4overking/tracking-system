FROM php:7.4.10-fpm-buster

LABEL maintainer="Oleg Bondarenko <overking@inbox.ru>"

ENV COMPOSER_VERSION 2.0.14

RUN apt update && apt install -y \
    imagemagick \
    libcurl4-openssl-dev \
    libxml2-dev \
    zip \
    libonig-dev \
    zlib1g-dev \
    libzip-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libjpeg-dev \
    libmagickwand-dev \
    libpng-dev \
    libpcre3 \
    libpcre3-dev \
    autoconf \
    gcc \
    g++ \
    libc-dev \
    make \
    wget \
    nodejs \
    npm \
    && pecl install xdebug \
    && pecl install redis && echo "extension=redis.so" > /usr/local/etc/php/conf.d/ext-redis.ini \
    && pecl install imagick && echo "extension=imagick.so" > /usr/local/etc/php/conf.d/ext-imagick.ini \
    && docker-php-ext-install \
        ctype \
        curl \
        dom \
        iconv \
        json \
        intl \
        mbstring \
        pdo \
        pdo_mysql \
        xml \
        zip \
        session \
        simplexml \
        tokenizer

ADD https://raw.githubusercontent.com/mlocati/docker-php-extension-installer/master/install-php-extensions /usr/local/bin/

RUN chmod uga+x /usr/local/bin/install-php-extensions && sync && \
    install-php-extensions gd

RUN curl --insecure https://getcomposer.org/download/${COMPOSER_VERSION}/composer.phar -o /usr/bin/composer && chmod +x /usr/bin/composer

RUN curl -L https://cs.symfony.com/download/php-cs-fixer-v2.phar -o /usr/local/bin/php-cs-fixer && chmod a+x /usr/local/bin/php-cs-fixer

COPY php.ini  /usr/local/etc/php/php.ini
COPY xdebug.ini  /usr/local/etc/php/conf.d/xdebug.ini

WORKDIR /var/www/project

STOPSIGNAL SIGTERM
