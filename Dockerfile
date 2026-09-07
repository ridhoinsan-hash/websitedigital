FROM php:8.2-apache

# Matikan mpm_event dan mpm_worker secara eksplisit sebelum mengaktifkan rewrite/prefork
RUN a2dismod mpm_event mpm_worker \
    && a2enmod mpm_prefork rewrite \
    && docker-php-ext-install mysqli pdo pdo_mysql

COPY . /var/www/html/

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Script start agar listen ke $PORT Railway
RUN printf '%s\n' \
  '#!/bin/bash' \
  'set -e' \
  'PORT="${PORT:-80}"' \
  'sed -i "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf' \
  'sed -i "s/:80/:${PORT}/" /etc/apache2/sites-available/000-default.conf' \
  'exec apache2-foreground' \
  > /usr/local/bin/start.sh \
  && chmod +x /usr/local/bin/start.sh

CMD ["/usr/local/bin/start.sh"]
