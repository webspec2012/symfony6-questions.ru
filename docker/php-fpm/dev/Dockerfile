# docker/php-fpm/Dockerfile

# PHP-FPM 8.1 (from Alpine)
# https://github.com/docker-library/php/blob/master/8.1/alpine3.14/fpm/Dockerfile
FROM php:8.1-fpm-alpine

# Работа от указанного пользователя
ARG USER_UID
ARG USER_GID

RUN apk add --no-cache shadow
RUN usermod -u ${USER_UID} www-data && groupmod -g ${USER_GID} www-data

COPY www.conf /usr/local/etc/php-fpm.d/www.conf

# Установка пакетов
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
RUN apk add --update --no-cache git bash autoconf gcc make g++ zlib-dev npm libxslt
RUN apk add --update --no-cache \
    libzip-dev \
    libpng-dev \
    icu-dev \
    oniguruma-dev \
    libsodium-dev \
    rabbitmq-c rabbitmq-c-dev

RUN docker-php-ext-install \
    opcache \
    zip \
    intl \
    gd \
    pdo \
    pdo_mysql \
    sysvsem

RUN pecl install -o -f redis \
	&&  rm -rf /tmp/pear \
	&&  docker-php-ext-enable redis

RUN pecl install -o -f amqp \
	&&  rm -rf /tmp/pear \
	&&  docker-php-ext-enable amqp

# Установка workdir
WORKDIR /var/www

# Установка supervisor
RUN apk add --no-cache supervisor
COPY supervisor.conf /etc/supervisor.conf

# Запуск
EXPOSE 9000
CMD ["supervisord", "-c", "/etc/supervisor.conf"]
