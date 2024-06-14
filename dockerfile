# Usa una imagen oficial de PHP 8.2 con Apache
FROM php:8.2-apache

# Instala extensiones de PHP necesarias
RUN apt-get update && apt-get install -y libpng-dev libjpeg-dev libfreetype6-dev libzip-dev zip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql zip

# Instala Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copia el contenido del proyecto al contenedor
COPY . /var/www/html

# Establece permisos
RUN chown -R www-data:www-data /var/www/html \
    && a2enmod rewrite

# Instala dependencias de Composer
RUN composer install --no-dev --optimize-autoloader

# Copia el archivo de configuración de Apache
COPY .docker/apache/vhost.conf /etc/apache2/sites-available/000-default.conf

# Expone el puerto 80
EXPOSE 80

# Establece el comando por defecto para ejecutar Apache
CMD ["apache2-foreground"]
