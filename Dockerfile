FROM php:8.4-fpm

# Install dependencies
RUN apt-get update -y && apt-get install -y \
    nginx \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libicu-dev \
    zip unzip curl git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo pdo_mysql mbstring xml gd zip intl opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Nginx config
RUN echo 'server {\n\
    listen ${PORT};\n\
    root /var/www/html/public;\n\
    index index.php;\n\
    location / {\n\
        try_files $uri $uri/ /index.php?$query_string;\n\
    }\n\
    location ~ \\.php$ {\n\
        fastcgi_pass 127.0.0.1:9000;\n\
        fastcgi_index index.php;\n\
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;\n\
        include fastcgi_params;\n\
    }\n\
}' > /etc/nginx/sites-available/default

# Startup script
RUN printf '#!/bin/bash\nset -e\n\
# Replace PORT placeholder in nginx config\n\
sed -i "s/\\${PORT}/${PORT:-80}/" /etc/nginx/sites-available/default\n\
# Laravel setup\n\
php artisan config:cache\n\
php artisan route:cache\n\
php artisan migrate --force --no-interaction\n\
php artisan db:seed --class=UserSeeder --force\n\
php artisan storage:link --force 2>/dev/null || true\n\
# Start PHP-FPM in background\n\
php-fpm -D\n\
# Start Nginx in foreground\n\
exec nginx -g "daemon off;"\n' > /start.sh && chmod +x /start.sh

EXPOSE 80

CMD ["/bin/bash", "/start.sh"]
