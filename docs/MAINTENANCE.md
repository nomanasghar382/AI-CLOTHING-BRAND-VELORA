# Maintenance Guide

## Routine tasks

### Daily

- Monitor admin system health (`/admin/system`)
- Review failed queue jobs: `php artisan queue:failed`
- Check slow request logs for `velora.slow_request`

### Weekly

- Review search analytics table for trending queries
- Export admin reports if required by operations
- Verify backup integrity for MySQL

### Monthly

- Rotate application logs
- Review Cloudinary storage usage
- Update dependencies after running test suites

## Cache management

Clear caches from admin system health or CLI:

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

For Redis production caches, prefer targeted cache tags when introduced.

## Queue maintenance

```bash
php artisan queue:work
php artisan queue:retry all
php artisan queue:flush
```

Supplier inventory sync and product alert jobs depend on active workers.

## Database maintenance

```bash
php artisan migrate
php artisan db:seed --class=SearchSynonymSeeder
```

Re-seed only in non-production environments unless explicitly required.

## Search optimization

- Synonyms: `search_synonyms` table via `SearchSynonymSeeder`
- Analytics: `search_analytics` updated on each search
- Recent history: `search_histories` per user or session

## PWA updates

Service worker uses `autoUpdate`. After frontend deploy, users receive updated assets on next visit. For breaking API changes, coordinate backend and frontend releases together.

## Incident response

1. Check `/up` and admin system health
2. Inspect `storage/logs/laravel.log`
3. Verify database and Redis connectivity
4. Pause queue workers during data fixes if needed
5. Communicate maintenance via admin notifications

## Security maintenance

- Rotate Sanctum tokens policy as needed
- Review admin activity logs
- Keep `APP_DEBUG=false` in production
- Audit `.env` secrets and revoke unused API keys
