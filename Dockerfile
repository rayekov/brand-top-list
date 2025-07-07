FROM php:8.3-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    default-mysql-client \
    apache2 \
    openssl \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy composer files first for better caching
COPY composer.json composer.lock ./

# Install dependencies 
RUN composer install --optimize-autoloader --no-scripts

# Copy application code
COPY . .

RUN ls -l /var/www/html

RUN mkdir -p /var/www/html/var

# Set proper permissions 
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 777 /var/www/html/var

# Configure Apache
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf
RUN a2enmod rewrite proxy proxy_fcgi

# Copy start.sh script
COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

# Expose port 80 for Apache
EXPOSE 80

# Start Apache and PHP-FPM
CMD ["/start.sh"]