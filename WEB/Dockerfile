FROM php:8.2-apache


RUN apt-get update && apt-get install -y git unzip \
    && docker-php-ext-install mysqli \
    && docker-php-ext-enable mysqli \
    && a2enmod rewrite


COPY --from=composer:latest /usr/bin/composer /usr/bin/composer


ENV APACHE_DOCUMENT_ROOT /var/www/html/WEB
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/conf-available/*.conf


WORKDIR /var/www/html
COPY . .


WORKDIR /var/www/html/WEB
RUN composer install --no-dev --optimize-autoloader