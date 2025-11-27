#!/bin/bash

echo "=== Despliegue Laravel iniciado ==="

# Instalar dependencias PHP
composer install --no-dev --optimize-autoloader

# Instalar dependencias JS y compilar assets
npm install
npm run build

# Migrar base de datos (opcional, solo si necesitas migraciones)
php artisan migrate --force

# Limpiar y cachear configuración
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Generar clave de aplicación si no existe
if ! grep -q "APP_KEY=" .env; then
    php artisan key:generate
fi

# Permisos para storage y cache
chmod -R 775 storage bootstrap/cache

echo "=== Despliegue Laravel finalizado ==="