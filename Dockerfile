FROM php:8.5-cli

ENV APP_HOME /usr/src/app
WORKDIR $APP_HOME
ENV COMPOSER_ALLOW_SUPERUSER 1

RUN apt-get update \
    && apt-get -y install \
    zlib1g-dev libzip-dev \
    ripgrep tree fd-find jq unzip curl htop procps git less \
    && docker-php-ext-install zip \
    && pecl install pcov \
    && docker-php-ext-enable pcov

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . ./
