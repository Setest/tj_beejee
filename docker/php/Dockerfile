FROM php:8.2-fpm

RUN apt-get update &&\
    apt-get install -y apt-utils \
      libpq-dev libpng-dev  \
      libzip-dev zip unzip \
      curl \
      locales \
      locales-all

RUN apt-get clean
RUN rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Xdebug
RUN pecl install xdebug
RUN docker-php-ext-enable xdebug

COPY --from=composer:2.5.4 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

CMD ["php-fpm"]