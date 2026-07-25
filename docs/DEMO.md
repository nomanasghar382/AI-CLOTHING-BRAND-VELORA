# VELORA Demo Package

## Quick Start

```bash
cd backend
cp .env.example .env
# Set VELORA_DEMO_MODE=true in .env
composer install
php artisan key:generate
php artisan migrate:fresh --seed
php artisan serve
```

```bash
cd frontend
npm install
npm run dev
```

## Demo Accounts

Password for all accounts: `VeloraDemo!2026`

| Role | Email | Portal |
|------|-------|--------|
| Super Admin | admin@velora.test | `/admin` |
| Customer | customer@velora.test | Storefront |
| Creator | creator@velora.test | Creator tools |
| Supplier | supplier@velora.test | `/supplier` |

## Demo Data Included

When `VELORA_DEMO_MODE=true`:

- **1,000 products** across 50 categories and 100 brands
- **8 demo orders** with payments across order statuses
- **Creator profile** with collections, lookboards, and followers
- **Supplier profile** with inventory and warehouse address
- **Community posts** with comments and likes
- **Wardrobe items** and visual search history
- **Loyalty transactions** with points balance
- **Support tickets** (open and resolved)
- **14 days** of BI analytics events and sales forecasts
- **Notifications** for order status updates
- **Ethical passports** on published products
- **Admin notifications** and activity logs

## Demonstration Workflows

### Guest
1. Browse catalog at `/catalog`
2. Use instant search and filters
3. View product detail with ethical passport

### Customer
1. Login as `customer@velora.test`
2. Add items to cart and wishlist
3. View orders and loyalty wallet
4. Explore wardrobe and visual search
5. Browse community feed

### Creator
1. Login as `creator@velora.test`
2. View creator profile and collections
3. Browse lookboards

### Supplier
1. Login as `supplier@velora.test`
2. View supplier portal inventory
3. Review profile and certifications

### Administrator
1. Login as `admin@velora.test`
2. Open admin dashboard
3. Press `⌘K` for command palette
4. Review operations center and system health
5. Export reports

## Reset Demo Data

```bash
VELORA_DEMO_MODE=true php artisan migrate:fresh --seed
```
