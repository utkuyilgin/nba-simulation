# Base image
FROM php:8.2-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    git \
    libicu-dev \
    curl \
    ca-certificates \
    apt-transport-https \
    lsb-release \
    libzip-dev

# Install extensions (including pcntl for Reverb)
RUN docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install gd pdo pdo_mysql intl pcntl zip

# Install Node.js and npm
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory inside the container
WORKDIR /var/www

# Copy the existing application directory contents
COPY . .

# Install PHP dependencies via Composer
RUN composer install

# Install frontend dependencies via npm
RUN npm install && npm run build

# Expose ports (9001 for Laravel, 8080 for Reverb)
EXPOSE 9001 8080

# Default command to start Laravel and Reverb
CMD ["bash", "-c", "php artisan serve --host=0.0.0.0 --port=9001"]
