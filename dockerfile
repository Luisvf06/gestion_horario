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
    npm \
    libonig-dev \
    libicu-dev \
    libcurl4-openssl-dev \
    libssl-dev \
    libsodium-dev \
    libzip-dev \
    libldap2-dev \
    libxml2-dev \
    libmcrypt-dev \
    libsqlite3-dev

# Limpiar cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Configurar e instalar extensiones de PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-configure intl && \
    docker-php-ext-configure ldap --with-libdir=lib/x86_64-linux-gnu && \
    docker-php-ext-install -j$(nproc) \
    curl \
    fileinfo \
    gd \
    intl \
    mbstring \
    exif \
    openssl \
    pdo_mysql \
    soap \
    sockets \
    sodium \
    xsl \
    zip

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Establecer la variable de entorno para Composer
ENV COMPOSER_ALLOW_SUPERUSER 1

# Copiar archivos de la aplicación
COPY . /var/www/html

# Instalar dependencias de Node.js
RUN npm install --production

# Cambiar propietario de los archivos a www-data
RUN chown -R www-data:www-data /var/www/html

# Cambiar el usuario actual a www-data
USER www-data

# Exponer el puerto 9000 y iniciar el servidor PHP-FPM
EXPOSE 9000
CMD ["php-fpm"]
