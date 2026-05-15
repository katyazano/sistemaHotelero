#!/bin/bash
set -e

echo "=== Esperando conexión a MySQL ==="
for i in {1..15}; do
  php artisan migrate --force 2>/dev/null && break
  echo "Intento $i fallido, esperando 4 segundos..."
  sleep 4
done

echo "=== Ejecutando migración final ==="
php artisan migrate --force

echo "=== Ejecutando seeder ==="
php artisan db:seed --force

echo "=== Optimizando configuración ==="
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "=== Iniciando servidor en puerto $PORT ==="
php artisan serve --host=0.0.0.0 --port=$PORT
