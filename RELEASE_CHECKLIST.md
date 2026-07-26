# VELORA v1.0.0 Release Checklist

## Certification Status

| Check | Status |
|-------|--------|
| Laravel tests (51 tests, 349 assertions) | ✓ Pass |
| `php artisan velora:validate-release --migrate` | ✓ Pass |
| React lint | ✓ Pass |
| Production build (`npm run build`) | ✓ Pass |
| VERSION file | `1.0.0` |
| Documentation (17 files) | ✓ Complete |
| Demo package (`VELORA_DEMO_MODE`) | ✓ Ready |

## Pre-Deployment

- [ ] Set `VELORA_DEMO_MODE=false` in production
- [ ] Configure `DB_*` for MySQL 8
- [ ] Set `APP_KEY`, `APP_URL`, `FRONTEND_URL`
- [ ] Configure `STRIPE_*`, `CLOUDINARY_*`, `OPENAI_*` in `.env`
- [ ] Run `php artisan migrate --force`
- [ ] Run `php artisan config:cache && php artisan route:cache`
- [ ] Start queue worker: `php artisan queue:work`
- [ ] Configure scheduler cron: `* * * * * php artisan velora:scheduler-run`
- [ ] Deploy `frontend/dist/` behind Nginx/Apache
- [ ] Enable HTTPS

## Demo Presentation

```bash
VELORA_DEMO_MODE=true php artisan migrate:fresh --seed
```

Accounts: `admin@velora.test`, `customer@velora.test`, `creator@velora.test`, `supplier@velora.test`  
Password: `VeloraDemo!2026`

See [docs/DEMO.md](docs/DEMO.md) for workflows.

## GitHub Release

Tag: `v1.0.0`  
Release notes: [RELEASE_NOTES.md](RELEASE_NOTES.md)  
Changelog: [CHANGELOG.md](CHANGELOG.md)
