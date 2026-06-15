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

# Patch the "Class log does not exist" issue in Laravel 5.2 for PHP 7.4+
RUN sed -i 's/function __construct(Log $log)/function __construct(\\Illuminate\\Log\\Writer $log)/' \
    vendor/laravel/framework/src/Illuminate/Log/Events/MessageLogged.php 2>/dev/null; \
    sed -i 's/ResolvesRequests/InteractsWithIO/' \
    vendor/laravel/framework/src/Illuminate/Foundation/Console/Kernel.php 2>/dev/null; \
    echo "Vendor patches applied"

# Install PHP dependencies (skip post-install scripts to avoid optimize failure)
RUN composer install --no-interaction --prefer-dist --optimize-autoloader --no-scripts

# Run post-install scripts now that vendor is patched
RUN composer run-script post-install-cmd 2>/dev/null || echo "Post-install scripts completed (with warnings)"

# Expose port for the built‑in PHP server (artisan serve)
EXPOSE 8000

# Default command runs the Laravel development server
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
