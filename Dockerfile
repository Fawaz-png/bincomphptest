FROM php:8.2-fpm

# Install dependencies
# Just installing system dependencies without PHP extensions
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libjpeg-dev libfreetype6-dev \
    libonig-dev libxml2-dev libicu-dev libmcrypt-dev libxslt1-dev \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql pgsql\
    && apt-get clean && rm -rf /var/lib/apt/lists/*
    


# Install Composer (pinned)
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy app files
COPY . .

# Fallback .env
COPY .env.example .env

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Set permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 storage bootstrap/cache

# Switch to non-root user
USER www-data

# Expose port & healthcheck
EXPOSE 8080

HEALTHCHECK CMD curl -f http://localhost:8080 || exit 1

# Start Laravel
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8080"]
