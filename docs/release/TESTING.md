# Testing Guide

## Backend

```bash
cd backend
php artisan test
```

### Test Suites

- **Authentication** — register, login, profile, security
- **Catalog** — products, search, admin catalog
- **Shopping** — cart, checkout, orders, admin shopping
- **Creator & Community** — profiles, wardrobe, posts
- **Loyalty & Passport** — wallet, rewards, ethical passports
- **Admin** — dashboard, settings, support, reports
- **Enterprise** — operations, webhooks, security hardening
- **Release** — demo seeder, workspace, search, workflows

### Release Validation

```bash
php artisan velora:validate-release
php artisan velora:validate-release --json
```

## Frontend

```bash
cd frontend
npm run lint
npm run build
```

## Manual Workflow Testing

1. Register or login as `customer@velora.test`
2. Browse catalog, add to cart, checkout
3. View orders and loyalty wallet
4. Login as `creator@velora.test` — manage profile, lookboards, wardrobe
5. Login as `supplier@velora.test` — view inventory and profile
6. Login as `admin@velora.test` — dashboard, command palette (⌘K), global search
7. Verify PWA install prompt and offline page

## CI

GitHub Actions runs backend tests and frontend lint/build on every push. See `.github/workflows/ci.yml`.

## Accessibility Testing

- Keyboard navigation through admin command palette
- Skip links on storefront pages
- Reduced motion media query support
- Focus-visible styles on interactive elements
