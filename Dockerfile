# Resmi PHP-Apache imajını temel alıyoruz. Bu, PHP ve Apache sunucusunun
# birlikte kurulu olduğu hazır bir şablondur.
FROM php:8.2-apache

# Projemizin çalışması için gerekli olan PDO ve SQLite kütüphanelerini
# Docker kutusunun içine kuruyoruz.
RUN docker-php-ext-install pdo pdo_sqlite

# Kodlarımızı Docker kutusunun içindeki web sunucusu klasörüne kopyalıyoruz.
# Bilgisayarımızdaki tüm dosyaları (.) alıp, kutunun içindeki /var/www/html/ klasörüne koyar.
COPY . /var/www/html/

# Dosya ve klasörlerin web sunucusu tarafından okunabilir olması için
# doğru izinleri veriyoruz.
RUN chown -R www-data:www-data /var/www/html && chmod -R 755 /var/www/html