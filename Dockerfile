FROM php:8.2-fpm

# Install system + PostgreSQL dev dependencies
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libjpeg-dev libfreetype6-dev \
    libonig-dev libxml2-dev libicu-dev libmcrypt-dev libxslt1-dev \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql pgsql \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy composer files and install dependencies
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader

# Copy rest of the application
COPY . .

# Fallback .env
COPY .env.example .env

# Set permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 storage bootstrap/cache

# Switch to non-root user
USER www-data

# Expose port & healthcheck
EXPOSE 8080
HEALTHCHECK CMD curl -f http://localhost:8080 || exit 1

# 🧠 CMD to run Laravel optimizations and start the server
CMD php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache && \
    php artisan migrate --force && \
    php artisan serve --host=0.0.0.0 --port=8080
