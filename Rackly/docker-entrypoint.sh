#!/bin/bash
set -e

# Sesuaikan port Apache jika Render menyediakan environment variable $PORT
PORT="${PORT:-80}"
echo "==> Mengonfigurasi Apache untuk port: $PORT"
sed -i "s/Listen 80/Listen $PORT/g" /etc/apache2/ports.conf 2>/dev/null || true
sed -i "s/<VirtualHost \*:80>/<VirtualHost \*:$PORT>/g" /etc/apache2/sites-available/*.conf 2>/dev/null || true

# Pastikan folder storage dan bootstrap cache ada dan memiliki permission yang benar
echo "==> Memeriksa struktur direktori storage..."
mkdir -p /var/www/html/storage/framework/{sessions,views,cache}
mkdir -p /var/www/html/storage/logs
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Buat symbolic link storage publik
php artisan storage:link || true

# Jalankan migrasi database dan seeding data awal ke TiDB Cloud
echo "==> Menjalankan migrasi database ke TiDB Cloud..."
php artisan migrate --force --seed || echo "Migrasi database dilewati atau sudah up-to-date."

# Optimasi caching produksi Laravel
echo "==> Mengoptimalkan cache konfigurasi, rute, dan view..."
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Jalankan Apache Web Server
echo "==> Memulai Apache Web Server pada port $PORT..."
exec apache2-foreground

