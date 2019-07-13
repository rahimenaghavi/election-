FROM php:5.6-fpm-alpine

RUN apk update; \
    apk upgrade;

RUN docker-php-ext-install mysqli \
&& docker-php-ext-install pdo_mysql

COPY public_html /var/www/html/
