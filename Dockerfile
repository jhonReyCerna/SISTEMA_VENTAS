# Dockerfile para Laravel en Railway con extensión GD
FROM php:8.2-fpm

# Instala dependencias del sistema y extensiones requeridas
RUN apt-get update \
    && apt-get install -y libpng-dev libjpeg-dev libfreetype6-dev zip git unzip libzip-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd zip pdo pdo_mysql

# Instala Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Establece el directorio de trabajo
WORKDIR /var/www

# Copia los archivos del proyecto
COPY . .

# Instala dependencias de Composer
RUN composer install --optimize-autoloader --no-interaction --no-scripts

# Da permisos a la carpeta de almacenamiento y caché
RUN chown -R www-data:www-data storage bootstrap/cache

# Expone el puerto 8000
EXPOSE 8000

# Comando por defecto para iniciar el servidor
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
