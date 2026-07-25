# VELORA Project Structure

```
velora/
├── backend/                    # Laravel 12 REST API
│   ├── app/
│   │   ├── Console/Commands/   # Artisan commands (scheduler, validation)
│   │   ├── Http/
│   │   │   ├── Controllers/Api/V1/   # Versioned API controllers
│   │   │   └── Middleware/           # Security, API version, uploads
│   │   ├── Jobs/               # Queue jobs (email, webhooks, backups)
│   │   ├── Models/             # Eloquent models (101 entities)
│   │   ├── Notifications/      # Database + mail notifications
│   │   ├── Services/           # Business logic layer
│   │   │   ├── Admin/
│   │   │   ├── Auth/
│   │   │   ├── Catalog/
│   │   │   ├── Enterprise/     # Audit, metrics, backups, webhooks
│   │   │   ├── Loyalty/
│   │   │   ├── Shopping/
│   │   │   └── Style/          # AI fashion engine
│   │   └── Traits/             # RespondsWithApi, etc.
│   ├── database/
│   │   ├── migrations/         # Schema migrations (ordered)
│   │   ├── seeders/            # Role, catalog, demo environment
│   │   └── factories/          # Test data factories
│   ├── routes/
│   │   ├── api.php             # v1 API routes
│   │   ├── api_v2.php          # v2 status endpoint
│   │   └── console.php         # Scheduler tasks
│   └── tests/                  # Feature + unit tests (47 tests)
│
├── frontend/                   # React 19 SPA + PWA
│   ├── src/
│   │   ├── components/       # Reusable UI (admin, catalog, shopping, feedback)
│   │   ├── context/            # Auth, cart, wishlist, loyalty, notifications
│   │   ├── hooks/              # useAuth, useCart, useAsyncAction, useMotionConfig
│   │   ├── pages/              # Route-level pages
│   │   │   ├── admin/          # Enterprise admin panel
│   │   │   ├── ai/             # AI stylist pages
│   │   │   ├── catalog/        # Product catalog + detail
│   │   │   ├── community/      # Community feed
│   │   │   ├── loyalty/        # Rewards + VIP
│   │   │   ├── shopping/       # Cart, checkout, orders
│   │   │   ├── supplier/       # Supplier portal
│   │   │   └── wardrobe/       # Digital wardrobe
│   │   ├── services/           # API client + domain services
│   │   └── utils/              # formatMoney, getApiErrorMessage
│   └── dist/                   # Production build output
│
├── docs/                       # Documentation
│   ├── release/                # Release manuals and checklists
│   ├── API.md
│   ├── DEPLOYMENT.md
│   ├── DEVELOPER.md
│   └── ...
│
├── docker/                     # Docker configuration
├── .github/workflows/ci.yml    # CI pipeline
├── CHANGELOG.md
├── RELEASE_NOTES.md
└── VERSION                     # Current release version
```

## Architecture Layers

| Layer | Location | Responsibility |
|-------|----------|----------------|
| Presentation | `frontend/src/pages/` | User interface, routing |
| API Client | `frontend/src/services/` | HTTP communication |
| Controllers | `backend/app/Http/Controllers/` | Request validation, response formatting |
| Services | `backend/app/Services/` | Business logic |
| Models | `backend/app/Models/` | Data access, relationships |
| Infrastructure | `backend/app/Jobs/`, `Console/` | Async processing, scheduling |

## Key Conventions

- API responses use `{ success, message, data, errors }` envelope
- API version prefix: `/api/v1/`
- Admin routes protected by Sanctum + role/permission middleware
- All secrets in `backend/.env` only
