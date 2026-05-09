# Use PHP Apache image for dynamic content
FROM php:8.2-apache

# Install mysqli and pdo_mysql extensions for MySQL connectivity
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Enable Apache mod_rewrite for URL rewriting
RUN a2enmod rewrite

# Install wait-for-it script to wait for MySQL
ADD https://raw.githubusercontent.com/vishnubob/wait-for-it/master/wait-for-it.sh /usr/local/bin/wait-for-it
RUN chmod +x /usr/local/bin/wait-for-it

# Set working directory
WORKDIR /var/www/html

# Copy all files to the container
COPY . /var/www/html/

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html

# Create startup script
RUN echo '#!/bin/bash\n\
wait-for-it mysql:3306 --timeout=60\n\
apache2-foreground' > /usr/local/bin/start.sh && \
chmod +x /usr/local/bin/start.sh

# Expose port 80
EXPOSE 80

# Use custom startup script
CMD ["/usr/local/bin/start.sh"]
