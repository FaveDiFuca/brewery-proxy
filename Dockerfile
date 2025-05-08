# Dockerfile per Brewery-Proxy App
# Creato da Max - Maggio 2025

FROM php:8.2-apache

# Installazione pacchetti necessari
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libpng-dev \
    libonig-dev

# Estensioni PHP
RUN docker-php-ext-install pdo pdo_mysql mbstring exif
RUN docker-php-ext-install bcmath
RUN a2enmod rewrite

# Copiamo e cancellazione di node
COPY . /var/www/html/
RUN rm -rf /var/www/html/node_modules

# Set Working Directory
WORKDIR /var/www/html

# Set permessi
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage



# Autorizzo la porta 80
EXPOSE 80

