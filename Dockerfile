# Используем официальный образ PHP
FROM php:8.3-cli-slim

# Обновляем пакеты и устанавливаем зависимости
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libpng-dev libonig-dev libxml2-dev libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql zip

# Устанавливаем Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Указываем рабочую директорию
WORKDIR /app

# Копируем файлы проекта
COPY . .

# Устанавливаем зависимости
RUN composer install --no-dev --optimize-autoloader

# Устанавливаем переменную окружения
ENV APP_ENV=prod

# Открываем порт 80
EXPOSE 80

# Устанавливаем команду запуска Symfony через встроенный сервер
CMD ["php", "-S", "0.0.0.0:80", "-t", "public"]
