FROM php:8.4-fpm-alpine

ARG user
ARG uid

RUN apk add --no-cache --virtual .build-deps \
    build-base \
    autoconf \
    $PHPIZE_DEPS \
    && apk add --no-cache \
    curl \
    zip \
    unzip \
    nodejs \
    npm \
    freetype-dev \
    oniguruma-dev \
    libzip-dev

RUN npm install -g pnpm

RUN pecl install swoole redis \
    && docker-php-ext-enable swoole redis

RUN docker-php-ext-install \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip

RUN apk del .build-deps

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

RUN addgroup -g ${uid} ${user} && \
    adduser -D -u ${uid} -G ${user} ${user}

USER ${user}

ENV PATH="/home/${user}/.composer/vendor/bin:${PATH}"

RUN composer global require laravel/installer


RUN if [ ! -f .env ]; then \
    cp .env.example .env; \
fi

RUN pnpm install

EXPOSE 9000

