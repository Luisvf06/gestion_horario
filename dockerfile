# Usa la imagen oficial de PHP con FPM
FROM php:8.2-fpm

# Set working directory
WORKDIR /var/www/html

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libxslt-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    nginx \
    nodejs \
    npm

# Limpiar cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar extensiones de PHP
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd xsl

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Establecer la variable de entorno para Composer
ENV COMPOSER_ALLOW_SUPERUSER 1

# Copiar archivos de la aplicación
COPY . /var/www/html

# Instalar dependencias de Node.js
RUN npm install

# Cambiar propietario de los archivos a www-data
RUN chown -R www-data:www-data /var/www/html

# Cambiar el usuario actual a www-data
USER www-data

# Exponer el puerto 9000 y iniciar el servidor PHP-FPM
EXPOSE 9000
CMD ["php-fpm"]
