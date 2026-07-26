# VELORA Database Documentation

See [docs/release/DATABASE.md](release/DATABASE.md) for the complete schema reference and ER diagram.

## Overview

- **Engine:** MySQL 8 (SQLite for automated tests)
- **ORM:** Laravel Eloquent
- **Migrations:** 15+ migration files, ordered by timestamp
- **Models:** 101 Eloquent models

## Core Domains

| Domain | Key Tables |
|--------|-----------|
| Auth | `users`, `roles`, `permissions`, `personal_access_tokens` |
| Catalog | `products`, `categories`, `brands`, `product_variants`, `product_images` |
| Shopping | `orders`, `order_items`, `payments`, `shopping_carts`, `coupons` |
| Creator | `creator_profiles`, `creator_collections`, `lookboards` |
| Community | `community_posts`, `community_comments`, `community_likes` |
| Wardrobe | `wardrobe_items`, `visual_searches` |
| Loyalty | `loyalty_wallets`, `loyalty_transactions`, `ethical_passports` |
| Supplier | `supplier_profiles`, `supplier_inventory` |
| Admin | `settings`, `activity_logs`, `support_tickets`, `report_exports` |
| Enterprise | `audit_logs`, `application_metrics`, `backup_runs`, `feature_flags` |
| BI | `bi_analytics_events`, `bi_forecasts`, `bi_dashboard_snapshots` |

## Seeding

```bash
php artisan migrate:fresh --seed
```

For demo presentation data:

```bash
VELORA_DEMO_MODE=true php artisan migrate:fresh --seed
```

## Performance

- Foreign keys indexed on all relationship columns
- Status columns indexed for filtering (`orders.status`, `products.status`)
- Soft deletes on `products` table
- Eager loading used in controllers to prevent N+1 queries
