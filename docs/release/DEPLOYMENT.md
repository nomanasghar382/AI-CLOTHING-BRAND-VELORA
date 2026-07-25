# Deployment Guide

## Requirements

- PHP 8.2+
- Composer 2
- Node.js 20+
- MySQL 8
- Nginx or Apache
- Redis (recommended for cache/queues at scale)

## Local Development

```bash
# Backend
cd backend
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate --seed

# Frontend
cd ../frontend
npm install
npm run dev
```

## Docker

```bash
docker compose up -d
```

See `docker-compose.yml` and `docs/DEPLOYMENT.md` for service configuration.

## Production Deployment

1. Set environment variables (see `docs/release/ENVIRONMENT.md`)
2. Run migrations: `php artisan migrate --force`
3. Build frontend: `npm ci && npm run build`
4. Cache config: `php artisan config:cache && php artisan route:cache`
5. Start queue worker: `php artisan queue:work --tries=3`
6. Add cron: `* * * * * php artisan velora:scheduler-run`

## Health Checks

- `GET /api/v1/admin/system/health` — application health
- `GET /api/v2/status` — API version status
- `php artisan velora:validate-release` — release validation

## Rollback

1. Restore database from latest backup
2. Deploy previous release artifact
3. Run `php artisan migrate:rollback` if schema changed
4. Clear caches: `php artisan cache:clear`
