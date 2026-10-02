#!/bin/bash
set -e

CONTAINER="bengkel-app"
FIRST_DEPLOY=false

cd "$(dirname "$0")"

echo "🚀 Deploying Bengkel App..."

if [ ! -f .env ]; then
    echo "❌ File .env tidak ditemukan di $(pwd)!"
    echo "💡 Buat file .env terlebih dahulu: cp .env.example .env"
    exit 1
fi

if ! docker ps -a --format '{{.Names}}' | grep -q "^${CONTAINER}$"; then
    FIRST_DEPLOY=true
    echo "📦 First deploy detected"
fi

if ! docker network inspect proxy &>/dev/null; then
    echo "🌐 Creating external network 'proxy'..."
    docker network create proxy
fi

echo "🔨 Building app image..."
docker compose build

echo "▶️  Starting container..."
docker compose up -d

echo "⏳ Waiting for container..."
sleep 5

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

echo "✅ Deploy selesai! https://bengkel.ffatahilah.my.id"
