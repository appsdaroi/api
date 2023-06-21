FROM php:8.1-apache

ENV DEBIAN_FRONTEND=noninteractive
ENV COMPOSER_ALLOW_SUPERUSER=1

# Instalação do composer
RUN apt-get update -qq && \
    apt-get install -qy \
    git \
    gnupg \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libmcrypt-dev \
    libicu-dev \
    libxml2-dev \
    libzip-dev \
    libonig-dev \
    wget \
    nano \
    unzip \
    zip && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer && \
    apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN apt-get install -y libfreetype6-dev libjpeg62-turbo-dev libpng-dev
RUN apt-get install -y libzip-dev
RUN apt-get install -y libonig-dev

RUN docker-php-ext-install -j$(nproc) opcache pdo_mysql intl xml soap
RUN docker-php-ext-install -j$(nproc) gd
RUN docker-php-ext-install -j$(nproc) zip
RUN docker-php-ext-install -j$(nproc) mbstring

WORKDIR /app/

RUN a2enmod rewrite

RUN mkdir -p /app/

COPY . /app/
RUN chown -R www-data:www-data /app

COPY ./docker-compose/apache/000-default.conf /etc/apache2/sites-available/000-default.conf

RUN /usr/local/bin/composer update --ignore-platform-req=ext-http
