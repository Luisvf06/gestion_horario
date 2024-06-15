# Usa una imagen base de PHP con FPM
FROM php:8.2-fpm

# Instalar dependencias del sistema y extensiones PHP necesarias
RUN apt-get update && apt-get install -y \
    libxslt-dev \
    && docker-php-ext-install xsl

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar el directorio de trabajo
WORKDIR /app

# Copiar archivos de la aplicación
COPY . .

# Instalar dependencias de PHP y Node.js
RUN composer install --no-dev --optimize-autoloader
RUN npm install --production

# Cachear configuración y optimizaciones de Laravel
RUN php artisan config:cache
RUN php artisan route:cache
RUN php artisan view:cache
RUN php artisan migrate --force
RUN php artisan db:seed --force

# Exponer el puerto 9000 y definir el comando predeterminado
EXPOSE 9000
CMD ["php-fpm"]
