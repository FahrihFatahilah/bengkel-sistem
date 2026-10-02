#!/bin/bash
set -e

CONTAINER="bengkel-app"
FIRST_DEPLOY=false

echo "🚀 Deploying Bengkel..."

if [ ! -f .env ]; then
    echo "❌ File .env tidak ditemukan!"
    exit 1
fi

if ! docker ps -a --format '{{.Names}}' | grep -q "^${CONTAINER}$"; then
    FIRST_DEPLOY=true
    echo "📦 First deploy detected"
fi

echo "🔨 Building app image..."
docker compose build

echo "▶️  Starting container..."
docker compose up -d

echo "⏳ Waiting for container..."
sleep 3

echo "🗄️  Running migrations..."
docker exec $CONTAINER php artisan migrate --force

if [ "$FIRST_DEPLOY" = true ]; then
    echo "🌱 Seeding database..."
    docker exec $CONTAINER php artisan db:seed --force
    echo "🔗 Creating storage link..."
    docker exec $CONTAINER php artisan storage:link
fi

echo "⚡ Caching..."
docker exec $CONTAINER php artisan config:cache
docker exec $CONTAINER php artisan route:cache
docker exec $CONTAINER php artisan view:cache
docker exec $CONTAINER php artisan storage:link

echo "✅ Deploy selesai! https://bengkel.ffatahilah.my.id"
