FROM php:8.2-apache

# Enable the rewrite module
RUN a2enmod rewrite

# Install the redis PHP extension
RUN pecl install redis && docker-php-ext-enable redis

# Set PHP session timeout to 21600 seconds / 6 hours
RUN echo "session.gc_maxlifetime = 21600" >> /usr/local/etc/php/php.ini
RUN echo "session.cookie_lifetime = 21600" >> /usr/local/etc/php/php.ini

# Configure Apache logging
RUN echo "ErrorLog /var/log/apache2/error.log" >> /etc/apache2/apache2.conf
RUN echo "LogLevel error" >> /etc/apache2/apache2.conf
RUN echo "CustomLog /dev/null combined" >> /etc/apache2/apache2.conf

# Start Apache
CMD ["apache2-foreground"]
