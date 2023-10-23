# Cria Dockerfile com PHP + Composer
# Autor: Lucas Ferreira
# Data: 20/08/2021
# ------------------------------------------------------------------------
FROM php:7.4-apache

# Instala o Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Habilita o mod_rewrite do Apache
RUN a2enmod rewrite

# Copia o arquivo de configuração do Apache
COPY ./config/apache.conf /etc/apache2/sites-available/000-default.conf

# Copia o arquivo de configuração do PHP
COPY ./config/php.ini /usr/local/etc/php/

# Copia o arquivo de configuração do Xdebug
COPY ./config/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

# instala dependencias do composer
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-install zip \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-install mysqli \
    && docker-php-ext-install opcache \
    && docker-php-ext-install bcmath \
    && docker-php-ext-install sockets \
    && docker-php-ext-install pcntl \
    && docker-php-ext-install exif \
    && docker-php-ext-install gettext \
    && docker-php-ext-install shmop

