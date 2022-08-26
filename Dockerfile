FROM composer:latest AS composer
FROM php:8.1-cli
WORKDIR /app
COPY --from=composer /usr/bin/composer /usr/bin/composer
COPY composer.json ./
COPY composer.lock ./
RUN pecl install redis && docker-php-ext-enable redis
RUN docker-php-ext-install pcntl
RUN apt-get update && apt-get install -y \
    zlib1g-dev \
    libzip-dev \
    unzip
RUN docker-php-ext-install zip
RUN composer update
COPY . .
CMD ["php", "test3.php"]
