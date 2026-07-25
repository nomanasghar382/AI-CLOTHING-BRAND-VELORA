# Deployment Checklist

- [ ] Pull release tag `v1.0.0-rc.1`
- [ ] Install backend dependencies (`composer install --no-dev --optimize-autoloader`)
- [ ] Install frontend dependencies and build (`npm ci && npm run build`)
- [ ] Copy `.env.example` to `.env` and configure secrets
- [ ] Run `php artisan key:generate`
- [ ] Run `php artisan migrate --force`
- [ ] Run `php artisan config:cache`
- [ ] Run `php artisan route:cache`
- [ ] Run `php artisan view:cache`
- [ ] Configure Nginx/Apache to serve frontend `dist/` and proxy `/api`
- [ ] Start PHP-FPM, queue worker, and scheduler
- [ ] Verify health endpoint `/api/v1/admin/system/health`
- [ ] Smoke test login, catalog, cart, and admin dashboard
