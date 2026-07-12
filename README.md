# FiveM-Catalog

A community marketplace for FiveM server resources — scripts, MLOs, EUP, and vehicles — plus a freelance developer services board. Built with Laravel 12, Tailwind CSS, and MySQL.

## Quick start (Docker)

Requires [Docker](https://docs.docker.com/get-docker/) and Docker Compose.

```bash
./install.sh
```

That's it — it copies the environment template, builds the images, boots the app, waits for the database, and runs migrations automatically. Then visit:

- **App:** http://localhost:8000
- **Adminer (DB browser):** http://localhost:8081 — server `db`, matching the `DB_USERNAME`/`DB_PASSWORD`/`DB_DATABASE` from your `.env`

To also seed demo data (an admin, a creator, and sample resources) on first boot, set `DB_SEED=true` in `.env` before running `./install.sh`.

If you don't have bash, run it manually instead:

```bash
cp .env.docker .env
docker compose up -d --build
```

Useful commands afterwards:

```bash
docker compose logs -f app        # tail app logs
docker compose exec app php artisan tinker
docker compose down               # stop
docker compose down -v            # stop and wipe all data (db + uploads)
```

## Production deployment (with HTTPS)

`docker-compose.prod.yml` adds [Caddy](https://caddyserver.com) as a reverse proxy in front of the app, which automatically requests and renews a Let's Encrypt certificate — no manual cert handling.

Requirements: a domain whose DNS already points at the server, with ports 80 and 443 reachable from the internet.

```bash
cp .env.docker .env
```

Edit `.env` and set:

```
APP_DOMAIN=fivem-catalog.com
APP_URL=https://fivem-catalog.com
ACME_EMAIL=you@example.com
SESSION_SECURE_COOKIE=true
```

Then:

```bash
./install-prod.sh
```

or manually:

```bash
docker compose -f docker-compose.yml -f docker-compose.prod.yml up -d --build
```

The app container itself is bound to `127.0.0.1` only (see `APP_PORT` in `.env.docker`) — all public traffic goes through Caddy on 80/443, so HTTPS can't be bypassed.

## Manual setup (without Docker)

Requires PHP 8.2+, Composer, Node.js, and MySQL.

```bash
composer install
cp .env.example .env
php artisan key:generate
# set DB_* in .env to point at your own MySQL instance
php artisan migrate
php artisan storage:link
npm install && npm run build
```

For local development with hot-reloading assets, a queue listener, and log tailing all at once:

```bash
composer dev
```

## Tech stack

- Laravel 12, MySQL, Blade + Alpine.js, Tailwind CSS, Vite
- Laravel Policies for authorization, database-backed sessions/cache/queue
