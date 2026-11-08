FROM php:8.2-apache

# Instala las dependencias y extensiones necesarias de forma directa
RUN apt-get update && apt-get install -y --fix-missing \
    libpq-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    && apt-get clean \
    && docker-php-ext-install pdo_pgsql mbstring exif pcntl bcmath gd

# Instala Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Habilita el mod_rewrite de Apache
RUN a2enmod rewrite

# Configura el DocumentRoot para que apunte a la carpeta public/
RUN sed -i -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/*.conf \
    && sed -i -e 's!/var/www/!/var/www/html/public!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf