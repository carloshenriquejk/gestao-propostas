FROM php:8.2-apache

# Instala dependências do PHP
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    mariadb-client \
    git \
    && docker-php-ext-install pdo_mysql zip mbstring exif pcntl bcmath gd

# Habilita mod_rewrite do Apache
RUN a2enmod rewrite

# Configura documento raiz
WORKDIR /var/www/html
COPY . /var/www/html

# Instala Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Permissões (Linux)
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

EXPOSE 80