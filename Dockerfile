# Use PHP 7.4 CLI image (compatible with Laravel 5.2)
FROM php:7.4-cli

# Install system dependencies and PHP extensions required by the project
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    && docker-php-ext-install pdo_mysql mbstring tokenizer zip xml bcmath

# Install Composer globally
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy project files into the container
COPY . /var/www/html

# Install PHP dependencies (ignore platform requirements because we are using compatible PHP version)
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Expose port for the built‑in PHP server (artisan serve)
EXPOSE 8000

# Default command runs the Laravel development server
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
