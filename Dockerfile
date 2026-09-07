FROM php:8.2-apache

RUN docker-php-ext-install mysqli pdo pdo_mysql \
    && a2enmod rewrite

COPY . /var/www/html/

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Script start yang membersihkan modul MPM ganda sebelum Apache berjalan
RUN printf '%s\n' \
  '#!/bin/bash' \
  'set -e' \
  'rm -f /etc/apache2/mods-enabled/mpm_event.*' \
  'rm -f /etc/apache2/mods-enabled/mpm_worker.*' \
  'PORT="${PORT:-80}"' \
  'sed -i "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf' \
  'sed -i "s/:80/:${PORT}/" /etc/apache2/sites-available/000-default.conf' \
  'exec apache2-foreground' \
  > /usr/local/bin/start.sh \
  && chmod +x /usr/local/bin/start.sh

CMD ["/usr/local/bin/start.sh"]
