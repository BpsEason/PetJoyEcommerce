# backend/Dockerfile
# 此 Dockerfile 用於建立 Laravel 後端服務的 Docker 映像檔。

# 使用 PHP 8.2 FPM 搭配 Alpine Linux
FROM php:8.2-fpm-alpine

# 安裝系統依賴
RUN apk add --no-cache \
    nginx \
    supervisor \
    mysql-client \
    curl \
    git \
    openssl \
    libxml2-dev \
    oniguruma-dev \
    && docker-php-ext-install pdo_mysql opcache \
    && apk add --no-cache nodejs npm \
    && npm install -g pnpm \
    && apk del --no-cache oniguruma-dev \
    && rm -rf /var/cache/apk/*

# 設定工作目錄
WORKDIR /var/www/html

# 安裝 Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# 複製應用程式程式碼
COPY . .

# 安裝 Composer 依賴
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# 複製 Nginx 配置
COPY ./nginx/nginx.conf /etc/nginx/conf.d/default.conf

# 設定目錄權限
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# 暴露埠號
EXPOSE 8000

# 啟動 PHP-FPM
CMD ["php-fpm"]
