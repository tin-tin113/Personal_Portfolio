FROM php:8.2-apache

# Install SQLite and required extensions
RUN apt-get update && apt-get install -y \
    libsqlite3-dev \
    sqlite3 \
    libpng-dev \
    libjpeg-dev \
    libwebp-dev \
    libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) gd pdo pdo_sqlite pdo_mysql \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

# Copy application files to web server document root
COPY . /var/www/html/

# Set proper permissions for SQLite db and uploads folder
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/database \
    && chmod -R 775 /var/www/html/uploads

# Configure Apache port to dynamic PORT environment variable used by Render
RUN sed -i 's/80/${PORT}/g' /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

ENV PORT=80

EXPOSE 80

CMD ["apache2-foreground"]
