FROM php:8.1-cli

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN apt-get update && apt-get install -y \
    unzip \
    git \
    libzip-dev \
    && docker-php-ext-install zip \
    && pecl install redis \
    && docker-php-ext-enable redis


WORKDIR /app

COPY composer.json ./
COPY composer.lock ./

RUN composer install

COPY . .

CMD ["/bin/bash"]
