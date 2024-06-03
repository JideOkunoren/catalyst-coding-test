FROM php:8.1-apache

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    default-mysql-client \
    libicu-dev \
    libxml2-dev \
    zlib1g-dev

RUN docker-php-ext-install mysqli pdo pdo_mysql intl

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

ENV PATH="root/.composer/vendor/bin:${PATH}"

COPY composer.json composer.lock ./

RUN composer install --no-interaction --prefer-dist

COPY app /var/www/html/app
COPY index.php /var/www/html/

RUN chown -R www-data:www-data /var/www/html
