#!/usr/bin/env bash
set -e

if [ ! -f .env ]; then
    echo "Creating .env from .env.docker..."
    cp .env.docker .env
    echo ""
    echo "Edit .env now and set at least:"
    echo "  APP_DOMAIN=yourdomain.com   (DNS must already point here)"
    echo "  APP_URL=https://yourdomain.com"
    echo "  ACME_EMAIL=you@example.com"
    echo "  SESSION_SECURE_COOKIE=true"
    echo ""
    echo "Then re-run ./install-prod.sh"
    exit 0
fi

if ! grep -q '^APP_DOMAIN=.\+' .env; then
    echo "APP_DOMAIN is not set in .env — required for Caddy to request a TLS certificate."
    exit 1
fi

if ! grep -q '^ACME_EMAIL=.\+' .env; then
    echo "ACME_EMAIL is not set in .env — required by Caddy's Let's Encrypt config."
    exit 1
fi

docker compose -f docker-compose.yml -f docker-compose.prod.yml up -d --build

DOMAIN=$(grep -m1 '^APP_DOMAIN=' .env | cut -d= -f2-)
echo ""
echo "FiveM-Catalog is starting up."
echo "  App:     https://${DOMAIN}"
echo "  Adminer: only reachable locally on the server, at 127.0.0.1:\$ADMINER_PORT"
echo ""
echo "Caddy is requesting a Let's Encrypt certificate for ${DOMAIN} — this can"
echo "take a few seconds and requires ports 80/443 to be reachable from the internet."
echo "Follow the logs with: docker compose -f docker-compose.yml -f docker-compose.prod.yml logs -f caddy app"
