FROM php:8.3-cli

# Install dependency sistem + ekstensi PostgreSQL + Node/NPM
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    libpq-dev \
    libzip-dev \
    zip \
    nodejs \
    npm \
    && docker-php-ext-install pdo pdo_pgsql pgsql zip

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy semua file project
COPY . .

# Install dependency PHP
RUN composer install --no-dev --optimize-autoloader

# Install dependency frontend + build Vite
RUN npm install && npm run build

# Pastikan folder writable
RUN mkdir -p storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Railway pakai PORT dari environment
CMD sh -c "php artisan serve --host=0.0.0.0 --port=${PORT:-8080}"