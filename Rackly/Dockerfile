# Gunakan image resmi PHP 8.3 dengan Apache (Debian Bookworm)
FROM php:8.3-apache

# Install dependensi sistem, pustaka C, dan sertifikat CA
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    ca-certificates \
    && rm -rf /var/lib/apt/lists/*

# Install Node.js 20.x & npm untuk kompilasi aset frontend Vite
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && rm -rf /var/lib/apt/lists/*

# Install ekstensi PHP yang dibutuhkan Laravel & TiDB MySQL
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Ambil binary Composer resmi versi 2
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Ubah konfigurasi DocumentRoot Apache mengarah ke folder public Laravel
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Aktifkan modul mod_rewrite Apache (wajib untuk routing Laravel)
RUN a2enmod rewrite

# Tentukan working directory aplikasi
WORKDIR /var/www/html

# Salin source code aplikasi
COPY . /var/www/html

# Install dependensi PHP via Composer (optimasi produksi)
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Install dependensi frontend & compile aset Vite
RUN npm install
RUN npm run build

# Atur kepemilikan dan permission direktori storage dan cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Salin script entrypoint untuk runtime startup
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN sed -i 's/\r$//' /usr/local/bin/docker-entrypoint.sh \
    && chmod +x /usr/local/bin/docker-entrypoint.sh

# Render menyediakan port dinamis via environment variable $PORT
EXPOSE 80 10000

ENTRYPOINT ["docker-entrypoint.sh"]

