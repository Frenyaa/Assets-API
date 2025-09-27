# Sử dụng image PHP chính thức
FROM php:8.2-fpm

# Cài đặt các phụ thuộc
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    unzip \
    git \
    default-mysql-client \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd zip pdo pdo_mysql

# Thiết lập thư mục làm việc
WORKDIR /var/www

# Cài đặt Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Sao chép mã nguồn vào container
COPY . .

# Cài đặt các phụ thuộc PHP
RUN composer install --no-dev --optimize-autoloader

# Mở cổng mà ứng dụng sẽ chạy
EXPOSE 9000

# Lệnh để chạy PHP-FPM
CMD ["php-fpm"]