# Imagen para desplegar BANDEK en un hosting gratuito basado en Docker
# (Render free tier). Ver DEPLOY-CLOUD.md para el paso a paso completo.
#
# Nota: usa el servidor embebido de PHP ("php artisan serve"), no
# Nginx+PHP-FPM. Para el tráfico bajo que va a tener el sitio al arrancar
# alcanza de sobra y mantiene la imagen simple; si el sitio crece, migrar a
# FrankenPHP o Nginx+FPM es el siguiente paso natural.

# ---- Etapa 1: compilar los assets (Tailwind/Vite) --------------------------
FROM node:20-slim AS assets

WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci

COPY resources ./resources
COPY vite.config.js tailwind.config.js postcss.config.js ./
RUN npm run build

# ---- Etapa 2: la app PHP ----------------------------------------------------
FROM php:8.3-cli

RUN apt-get update && apt-get install -y --no-install-recommends \
        libpng-dev libjpeg62-turbo-dev libwebp-dev libfreetype6-dev \
        libpq-dev libzip-dev libicu-dev libonig-dev \
        postgresql-client \
        unzip git \
    && docker-php-ext-configure gd --with-jpeg --with-webp --with-freetype \
    && docker-php-ext-install -j"$(nproc)" \
        pdo pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd intl zip \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .
COPY --from=assets /app/public/build ./public/build

RUN composer install --no-dev --optimize-autoloader --no-interaction \
    && chown -R www-data:www-data storage bootstrap/cache

# Render (y la mayoría de estos hostings) inyectan el puerto real en $PORT;
# 8080 es solo el valor por defecto para correrlo en local con `docker run`.
EXPOSE 8080

CMD php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache \
    && php artisan migrate --force \
    && php artisan serve --host=0.0.0.0 --port="${PORT:-8080}"
