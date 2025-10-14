# Gunakan image PHP bawaan dengan FPM
FROM php:8.2-fpm

# Install dependencies sistem
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd zip pdo pdo_mysql

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Atur direktori kerja
WORKDIR /var/www/html

# Copy file proyek ke container
COPY . .

# Install dependency Laravel
RUN composer install --no-dev --optimize-autoloader

# Jalankan artisan optimize
RUN php artisan config:clear && php artisan cache:clear && php artisan route:clear

# Jalankan server PHP bawaan
CMD php artisan serve --host=0.0.0.0 --port=8000

# Port default Laravel
EXPOSE 8000
