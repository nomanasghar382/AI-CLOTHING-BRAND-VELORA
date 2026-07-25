# VELORA Admin Guide

See [docs/release/ADMIN_MANUAL.md](release/ADMIN_MANUAL.md) for complete documentation.

## Access

Sign in at `/admin` with an admin account.

- Email: `admin@velora.test`
- Password: `VeloraDemo!2026`

## Command Center

The dashboard shows revenue, orders, catalog metrics, and recent orders.

## Command Palette

Press `⌘K` (Mac) or `Ctrl+K` (Windows/Linux) to:

- Search products, orders, customers, creators, suppliers
- Navigate to any admin section
- Run quick actions

## Key Sections

| Section | Purpose |
|---------|---------|
| Products | Catalog management |
| Orders | Order pipeline and fulfillment |
| Analytics | Revenue and order trends |
| Operations | Queues, scheduler, backups, feature flags |
| System Health | Runtime diagnostics |
| Settings | Shipping, tax, payment configuration |

## Demo Mode

Set `VELORA_DEMO_MODE=true` in `.env` and run `php artisan migrate:fresh --seed` to populate presentation data.

## Release Validation

```bash
php artisan velora:validate-release --migrate
```
