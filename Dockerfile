FROM php:8.2-apache

# System dependencies
RUN apt-get update && apt-get install -y \
    libpq-dev libzip-dev libpng-dev libjpeg-dev \
    zip unzip git curl \
    && rm -rf /var/lib/apt/lists/*

# PHP extensions
RUN docker-php-ext-install pdo pdo_pgsql zip gd opcache

# Apache: enable rewrite + headers modules
RUN a2enmod rewrite headers

# Node.js 20
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
 && apt-get install -y nodejs && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Install PHP deps (cached layer — only re-runs when composer files change)
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

# Install JS deps (cached layer — only re-runs when package files change)
COPY package.json package-lock.json vite.config.js tailwind.config.js postcss.config.js ./
RUN npm ci

# Copy full source, then build assets
COPY . .
RUN npm run build && rm -rf node_modules

# Run post-autoload-dump now that all files are present
RUN composer run-script post-autoload-dump --no-interaction 2>/dev/null || true

# Permissions for Laravel's writable directories
RUN chown -R www-data:www-data /var/www/html \
 && chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache

# Apache vhost: point document root at public/
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf

COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 80
CMD ["/start.sh"]
