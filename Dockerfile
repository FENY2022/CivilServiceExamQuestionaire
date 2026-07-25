FROM php:8.3-apache

RUN apt-get update && apt-get install -y \
    libicu-dev \
    libsqlite3-dev \
    unzip \
    && docker-php-ext-install intl pdo_sqlite \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . /var/www/html/

WORKDIR /var/www/html
RUN composer install --no-dev --optimize-autoloader --no-interaction

RUN mkdir -p writable/data \
    && chown -R www-data:www-data /var/www/html/writable \
    && chmod -R 775 /var/www/html/writable

ENV APACHE_DOCUMENT_ROOT=/var/www/html
RUN sed -i -e 's|/var/www/html|${APACHE_DOCUMENT_ROOT}|g' /etc/apache2/sites-available/*.conf /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

EXPOSE 80
