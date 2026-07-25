# VELORA

**Style, Intelligently Yours.** A production-oriented foundation for an AI-powered global modest-fashion marketplace.

## Architecture

```text
frontend/  React + Vite + Bootstrap 5
     ↓ REST / JSON
backend/   Laravel 12 + Sanctum
     ↓
MySQL
```

The current release intentionally includes only the core platform foundation: authentication, role-based access, API conventions, responsive UI primitives, layouts, and routing. Commerce, AI, payments, orders, dashboards, and analytics are deliberately excluded.

## Prerequisites

- Node.js 22+
- PHP 8.2+
- Composer 2+
- MySQL 8+

## Backend setup

```bash
cd backend
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

Create a MySQL database called `velora` and set the `DB_*` values in `backend/.env` before running migrations.

Seeded accounts use password `VeloraDemo!2026`:

- `admin@velora.test` — Super Admin
- `customer@velora.test` — Customer
- `creator@velora.test` — Creator
- `supplier@velora.test` — Supplier

## Frontend setup

```bash
cd frontend
cp .env.example .env
npm install
npm run dev
```

The frontend is available at `http://localhost:5173` and calls Laravel at `http://localhost:8000/api/v1`.

## Validation

```bash
cd backend && php artisan test
cd frontend && npm run lint && npm run build
```

## Backend folders

- `app/Contracts` — stable repository contracts
- `app/Repositories` — Eloquent data access implementations
- `app/Services` — application business logic
- `app/Http/Controllers/Api/V1` — versioned API controllers
- `app/Http/Requests` — request validation
- `app/Http/Resources` — response transformations
- `app/Support` and `app/Traits` — standardized API response utilities

## Frontend folders

- `src/components` — reusable UI and feedback components
- `src/layouts` — public and guest shell layouts
- `src/pages` — route-level screens
- `src/routes` — route guards and route map
- `src/context` — global authentication state
- `src/services` — Axios API clients
- `src/config` — environment-driven configuration

## API envelope

All Velora API endpoints return:

```json
{
  "success": true,
  "message": "Request completed successfully.",
  "data": {},
  "errors": null,
  "status": 200,
  "timestamp": "2026-07-25T10:00:00+00:00"
}
```

## Security baseline

- Laravel Sanctum personal access tokens
- Hashed passwords with strict password validation
- Email verification and password reset endpoints
- Role middleware for Super Admin, Admin, Customer, Creator, and Supplier
- API validation, rate limiting, CORS configuration, and safe ORM queries
- Secret values are environment variables; never commit `.env`
