FROM php:8.3-apache

COPY . /var/www/html/

RUN a2enmod rewrite \
    && chown -R www-data:www-data /var/www/html/data \
    && chmod -R 775 /var/www/html/data

EXPOSE 80
