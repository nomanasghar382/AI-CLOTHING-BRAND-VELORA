# Deployment Guide

## Production checklist

### Backend

1. Set `APP_ENV=production`, `APP_DEBUG=false`
2. Configure MySQL and run `php artisan migrate --force`
3. Set `CACHE_STORE=redis` and `QUEUE_CONNECTION=redis` when Redis is available
4. Run `php artisan config:cache`, `route:cache`, `view:cache`
5. Configure queue worker: `php artisan queue:work --tries=3`
6. Configure scheduler cron: `* * * * * php /path/to/artisan schedule:run`
7. Set `FRONTEND_URL` to the public React origin
8. Configure mail transport for transactional email
9. Set Cloudinary, Stripe and OpenAI keys in `.env`

### Frontend

1. Set `VITE_API_BASE_URL` to the production API URL
2. Build assets: `npm run build`
3. Serve `frontend/dist` via CDN or static host
4. Ensure HTTPS is enabled for PWA install and service worker scope

### Web server

- Route `/api` to Laravel `public/index.php`
- Serve React static files for all other paths (SPA fallback to `index.html`)
- Enable gzip/brotli compression
- Set long cache headers for hashed build assets

### Health monitoring

- Laravel health route: `GET /up`
- Admin system health API: `GET /api/v1/admin/system/health`
- Watch queue failed jobs and `velora.slow_request` log entries

### Security

- Never expose API keys to the React bundle
- Use Sanctum token authentication for protected routes
- Keep CORS restricted to the frontend origin
- Rate limiting is enabled on auth routes

## Zero-downtime releases

1. Deploy backend code
2. Run migrations
3. Restart queue workers
4. Deploy frontend build to CDN
5. Clear application cache from admin system health if needed

## Rollback

1. Revert deployment artifact
2. Roll back migrations only when safe for data
3. Clear config/route/view caches
4. Restart workers
