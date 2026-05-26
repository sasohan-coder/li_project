FROM php:8.2-apache

# Install PostgreSQL development libraries and PDO PGSQL extension
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Copy all project files to the web server root directory
COPY . /var/www/html/

# Expose port 80
EXPOSE 80
