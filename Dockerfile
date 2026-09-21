FROM php:8.2-apache

# Extensions PHP nécessaires (PDO MySQL + MongoDB)
RUN apt-get update && apt-get install -y \
        libssl-dev \
        pkg-config \
        unzip \
        git \
    && docker-php-ext-install pdo pdo_mysql \
    && pecl install mongodb \
    && docker-php-ext-enable mongodb \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Composer (pour la librairie mongodb/mongodb)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN if [ -f composer.json ]; then composer install --no-dev --optimize-autoloader --no-interaction || true; fi

# Le document root de l'application est le dossier public/
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e "s!/var/www/html!\${APACHE_DOCUMENT_ROOT}!g" /etc/apache2/sites-available/*.conf \
    && sed -ri -e "s!/var/www/!\${APACHE_DOCUMENT_ROOT}!g" /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf \
    && a2enmod rewrite

RUN chown -R www-data:www-data /var/www/html/storage
RUN a2dismod mpm_event || true && a2enmod mpm_prefork

EXPOSE 80
S