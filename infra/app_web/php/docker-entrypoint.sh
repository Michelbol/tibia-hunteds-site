#!/bin/sh
set -e

if [ -d /var/www/html/storage ] || [ -d /var/www/html/bootstrap/cache ]; then
  echo "Ajustando permissões de storage e bootstrap/cache..."
  mkdir -p /var/www/html/storage /var/www/html/storage/app /var/www/html/storage/framework /var/www/html/storage/framework/views /var/www/html/storage/logs /var/www/html/bootstrap/cache

  chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache || true
  chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache || true

  find /var/www/html/storage -type d -exec chmod 775 {} \; || true
  find /var/www/html/storage -type f -exec chmod 664 {} \; || true
  chmod -R 775 /var/www/html/bootstrap/cache || true
fi

exec "$@"