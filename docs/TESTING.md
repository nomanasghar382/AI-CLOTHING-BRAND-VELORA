# VELORA Testing Guide

See [docs/release/TESTING.md](release/TESTING.md) for detailed testing procedures.

## Automated Tests

```bash
cd backend && php artisan test
```

**Current coverage:** 51 tests, 349 assertions

### Test Suites

| Suite | Coverage |
|-------|----------|
| Authentication | Register, login, profile, security |
| Catalog | Products, search, admin catalog |
| Shopping | Cart, checkout, orders, returns, invoices |
| Creator & Community | Profiles, wardrobe, posts, moderation |
| Loyalty & Passport | Wallet, rewards, gift cards, passports |
| Admin | Dashboard, settings, support, reports, operations |
| Enterprise | Webhooks, security hardening, API versioning |
| Release | Demo seeder, workspace, workflow validation |

## Frontend Validation

```bash
cd frontend && npm run lint && npm run build
```

## Release Certification

```bash
cd backend && php artisan velora:validate-release --migrate
cd backend && php artisan velora:validate-release --with-tests
```

## Manual Workflow Testing

| Role | Key Flows |
|------|-----------|
| Guest | Browse catalog, search, view product detail |
| Customer | Register, login, cart, checkout, orders, loyalty, wardrobe |
| Creator | Profile, collections, lookboards, earnings |
| Supplier | Inventory, profile, operations |
| Admin | Dashboard, command palette (⌘K), global search, operations |

## Demo Accounts

Password: `VeloraDemo!2026`

| Email | Role |
|-------|------|
| admin@velora.test | Super Admin |
| customer@velora.test | Customer |
| creator@velora.test | Creator |
| supplier@velora.test | Supplier |
