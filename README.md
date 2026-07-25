# VELORA

**Style, Intelligently Yours.** Production-ready AI-powered global modest-fashion marketplace.

## Stack

| Layer | Technology |
| --- | --- |
| Frontend | React 19, Vite, Bootstrap 5, Framer Motion, PWA |
| Backend | Laravel 12, Sanctum, REST API |
| Database | MySQL 8 |
| Media | Cloudinary (server-side only) |
| Cache / Queue | Redis-ready (file/database drivers supported locally) |

## Platform modules

- Authentication and role-based access (customer, creator, supplier, admin)
- Product catalog with instant search, advanced filters and SEO
- Shopping (cart, wishlist, checkout, orders, coupons, returns)
- AI fashion engine (quiz, stylist chat, recommendations)
- Enterprise admin, supplier portal, creator commerce
- Community, wardrobe, visual search
- International commerce, loyalty, ethical passport
- Business intelligence and trend forecasting
- Production optimization (PWA, performance, accessibility, notifications)

## Prerequisites

- Node.js 22+
- PHP 8.2+
- Composer 2+
- MySQL 8+

## Installation

### Backend

```bash
cd backend
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

Create a MySQL database named `velora` and configure `DB_*` in `backend/.env`.

### Frontend

```bash
cd frontend
cp .env.example .env
npm install
npm run dev
```

Frontend: `http://localhost:5173`  
API: `http://localhost:8000/api/v1`

## Demo accounts

Password: `VeloraDemo!2026`

| Email | Role |
| --- | --- |
| admin@velora.test | Super Admin |
| customer@velora.test | Customer |
| creator@velora.test | Creator |
| supplier@velora.test | Supplier |

## Validation

```bash
cd backend && php artisan test
cd frontend && npm run lint && npm run build
```

## Environment variables

All external credentials are read from `backend/.env` only. Never expose secrets to React.

Key backend variables:

| Variable | Purpose |
| --- | --- |
| `APP_URL` | Laravel application URL |
| `FRONTEND_URL` | React app URL for emails and redirects |
| `DB_*` | MySQL connection |
| `CACHE_STORE` | `file` locally, `redis` in production |
| `QUEUE_CONNECTION` | `database` locally, `redis` in production |
| `OPENAI_API_KEY` | AI stylist (optional) |
| `STRIPE_*` | Payments (test mode) |
| `CLOUDINARY_*` | Media uploads |

Frontend only needs:

| Variable | Purpose |
| --- | --- |
| `VITE_API_BASE_URL` | Laravel API base URL |

## Documentation

- [Developer Guide](docs/DEVELOPER.md)
- [Deployment Guide](docs/DEPLOYMENT.md)
- [API Overview](docs/API.md)
- [Maintenance Guide](docs/MAINTENANCE.md)

## Architecture

```text
frontend/  React SPA + PWA service worker
     ↓ REST / JSON (Sanctum bearer tokens)
backend/   Laravel API (controllers → services → repositories)
     ↓
MySQL (+ Redis recommended in production)
```

## Production features

- Progressive Web App with offline fallback, install prompt and app shortcuts
- Instant search with debouncing, synonyms, analytics and suggestions
- Advanced catalog filters with saved presets (device-local)
- Toast notifications and notification center with unread counter
- WCAG-oriented accessibility (skip links, focus states, reduced motion)
- Dynamic SEO meta tags, Open Graph, Twitter cards and Schema.org product data
- Admin system health dashboard with cache management
- Professional email templates for lifecycle messaging
- API performance logging with `X-Response-Time` header

## License

Proprietary — VELORA platform source.
