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

RUN rm -f /etc/apache2/mods-enabled/mpm_event.load /etc/apache2/mods-enabled/mpm_event.conf /etc/apache2/mods-enabled/mpm_worker.load /etc/apache2/mods-enabled/mpm_worker.conf \
    && ln -sf /etc/apache2/mods-available/mpm_prefork.load /etc/apache2/mods-enabled/mpm_prefork.load \
    && ln -sf /etc/apache2/mods-available/mpm_prefork.conf /etc/apache2/mods-enabled/mpm_prefork.conf

RUN ls -la /etc/apache2/mods-enabled/ && echo "---APACHE2CONF---" && grep -i "loadmodule.*mpm" /etc/apache2/apache2.conf /etc/apache2/mods-enabled/*.load

EXPOSE 80