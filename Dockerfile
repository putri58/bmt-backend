FROM php:8.4-apache

# Install system dependencies dan PHP extensions
RUN apt-get update -y && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libicu-dev \
    zip \
    unzip \
    curl \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_mysql \
        mbstring \
        xml \
        gd \
        zip \
        intl \
        opcache \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy project files
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Apache VirtualHost config
RUN echo '<VirtualHost *:80>\n\
    DocumentRoot /var/www/html/public\n\
    <Directory /var/www/html/public>\n\
        AllowOverride All\n\
        Require all granted\n\
        Options -Indexes\n\
    </Directory>\n\
    ErrorLog ${APACHE_LOG_DIR}/error.log\n\
    CustomLog ${APACHE_LOG_DIR}/access.log combined\n\
</VirtualHost>' > /etc/apache2/sites-available/000-default.conf

# Startup script — Railway inject $PORT, Apache default 80
RUN echo '#!/bin/bash\n\
set -e\n\
# Gunakan PORT dari Railway, default 80\n\
APACHE_PORT=${PORT:-80}\n\
sed -i "s/Listen 80/Listen $APACHE_PORT/" /etc/apache2/ports.conf\n\
sed -i "s/*:80/*:$APACHE_PORT/" /etc/apache2/sites-available/000-default.conf\n\
# Cache config\n\
php artisan config:cache\n\
php artisan route:cache\n\
# Migrate\n\
php artisan migrate --force --no-interaction\n\
# Seed users\n\
php artisan db:seed --class=UserSeeder --force\n\
# Storage link\n\
php artisan storage:link --force 2>/dev/null || true\n\
# Start Apache\n\
exec apache2-foreground' > /start.sh \
    && chmod +x /start.sh

EXPOSE 80

CMD ["/bin/bash", "/start.sh"]
