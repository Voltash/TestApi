FROM php:8.1.9-fpm

WORKDIR /var/www

RUN usermod -u 1000 www-data

RUN apt-get update
RUN apt-get install -y \
    nano \
    curl \
    wget \
    git \
    libzip-dev \
    zip \
    unzip
    

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN docker-php-ext-install pdo_mysql
RUN docker-php-ext-enable opcache
RUN docker-php-ext-install zip

# php.ini
RUN echo "date.timezone=Europe/Amsterdam" >> /usr/local/etc/php/conf.d/docker-php-custom.ini
RUN echo "error_reporting=E_ALL & ~E_DEPRECATED & ~E_STRICT" >> /usr/local/etc/php/conf.d/docker-php-custom.ini

#clean
RUN apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*
