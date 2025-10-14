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
    nginx \
    supervisor \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd zip pdo pdo_mysql \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Atur direktori kerja
WORKDIR /var/www/html

# Copy composer files dulu (untuk caching layer)
COPY composer.json composer.lock ./

# Install dependency Laravel
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Copy semua file proyek
COPY . .

# Set permissions untuk storage dan bootstrap/cache
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Cache Laravel config
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

# Expose port
EXPOSE 8000

# Jalankan Laravel dengan serve
CMD php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
