FROM php:7.4-fpm

WORKDIR /var/www/html

# Setup GD extension
RUN apt-get update -y && apt-get install -y \
    libicu-dev \
    unzip zip \
    zlib1g-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libjpeg62-turbo-dev \
    libpq-dev

RUN docker-php-ext-install zip bcmath pdo pdo_pgsql pgsql
RUN docker-php-ext-configure gd --with-freetype --with-jpeg

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY /src/composer.* /var/www/html/

RUN groupadd -g 1000 www
RUN useradd -u 1000 -ms /bin/bash -g www www




COPY --chown=www:www . /var/www/html/

USER www
RUN composer install --no-dev --no-interaction --no-autoloader --no-scripts


COPY ./src /var/www/html/
RUN composer dump-autoload --optimize

EXPOSE 9000
CMD ["php-fpm"]