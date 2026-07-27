FROM php:8.3-cli

# Install required system packages and PHP extensions
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    libzip-dev \
    libpq-dev \
    && docker-php-ext-install \
        pdo \
        pdo_pgsql \
        zip

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copy Laravel project
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Expose Render port
EXPOSE 10000

# Start Laravel server
CMD ["sh", "-c", "php artisan serve --host=0.0.0.0 --port=${PORT:-10000}"]