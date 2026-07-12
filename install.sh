#!/usr/bin/env bash
set -e

if [ ! -f .env ]; then
    echo "Creating .env from .env.docker..."
    cp .env.docker .env
    DB_PASS=$(openssl rand -hex 24)
    sed -i.bak "s|^DB_PASSWORD=.*|DB_PASSWORD=${DB_PASS}|" .env && rm -f .env.bak
fi

docker compose up -d --build

echo ""
echo "FiveM-Catalog is starting up."
echo "  App:     $(grep -m1 '^APP_URL=' .env | cut -d= -f2-)"
echo "  Adminer: http://localhost:$(grep -m1 '^ADMINER_PORT=' .env | cut -d= -f2-)"
echo ""
echo "First boot runs migrations automatically — this can take a few seconds."
echo "Follow the logs with: docker compose logs -f app"
