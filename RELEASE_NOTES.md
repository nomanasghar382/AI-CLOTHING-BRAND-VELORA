# VELORA 1.0.0-rc.1 Release Notes

## Release Candidate Overview

VELORA 1.0.0-rc.1 is the first release candidate of the full-stack modest fashion commerce platform. This build integrates authentication, catalog, shopping, AI styling, creator commerce, community, wardrobe, visual search, international commerce, loyalty, ethical passports, supplier operations, business intelligence, admin tooling, PWA support, and enterprise infrastructure.

## Demo Environment

Enable demo data with:

```bash
VELORA_DEMO_MODE=true
php artisan migrate:fresh --seed
```

### Demo Accounts

| Role | Email | Password |
|------|-------|----------|
| Super Admin | admin@velora.test | VeloraDemo!2026 |
| Customer | customer@velora.test | VeloraDemo!2026 |
| Creator | creator@velora.test | VeloraDemo!2026 |
| Supplier | supplier@velora.test | VeloraDemo!2026 |

Demo data includes orders, payments, community posts, wardrobes, lookboards, loyalty transactions, support tickets, supplier inventory, analytics events, forecasts, and notifications.

## Admin Enhancements

- **Command palette**: Press `⌘K` / `Ctrl+K` in the admin panel
- **Global search**: Products, orders, customers, creators, suppliers, community, support
- **Quick actions**: Dashboard shortcuts for common operations
- **Recent activity**: Live activity log feed on the command center

## Validation

```bash
cd backend && php artisan velora:validate-release
cd backend && php artisan test
cd frontend && npm run lint && npm run build
```

## Upgrade Notes

- Set `VELORA_DEMO_MODE=false` in production
- Configure Stripe, Cloudinary, and OpenAI keys in Laravel `.env` only
- Run queue worker and scheduler in production

## Documentation

See `docs/release/` for API reference, database schema, deployment, environment variables, testing, and role-specific manuals.
