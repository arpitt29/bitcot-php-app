# Use the official PHP image with Apache
FROM php:8.2-apache

# Install the mysqli extension for MySQL connectivity
RUN docker-php-ext-install mysqli

# Copy application files to the web server's root directory
COPY index.php /var/www/html/