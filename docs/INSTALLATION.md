# VELORA Installation Guide

**Version:** 1.0.0

## Prerequisites

| Requirement | Version |
|-------------|---------|
| PHP | 8.2+ |
| Composer | 2.x |
| Node.js | 20+ |
| npm | 10+ |
| MySQL | 8.0+ |
| Git | 2.x |

Optional for production: Redis 7+, Docker, Nginx/Apache.

## 1. Clone Repository

```bash
git clone https://github.com/nomanasghar382/AI-CLOTHING-BRAND-VELORA.git velora
cd velora
```

## 2. Backend Setup

```bash
cd backend
cp .env.example .env
composer install
php artisan key:generate
```

### Configure `.env`

Minimum required variables:

```env
APP_NAME=VELORA
APP_URL=http://localhost:8000
FRONTEND_URL=http://localhost:5173

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=velora
DB_USERNAME=root
DB_PASSWORD=your_password

SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=database
```

Create the database:

```sql
CREATE DATABASE velora CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Run migrations and seed:

```bash
php artisan migrate --seed
```

Start the API server:

```bash
php artisan serve
```

API available at: `http://localhost:8000/api/v1`

## 3. Frontend Setup

```bash
cd ../frontend
npm install
```

Create `frontend/.env` (optional — defaults to localhost):

```env
VITE_API_BASE_URL=http://localhost:8000/api/v1
```

Start development server:

```bash
npm run dev
```

Frontend available at: `http://localhost:5173`

## 4. Demo Environment (Presentation)

For a fully populated demonstration database:

```bash
# In backend/.env
VELORA_DEMO_MODE=true

cd backend
php artisan migrate:fresh --seed
```

Demo accounts (password: `VeloraDemo!2026`):

| Role | Email |
|------|-------|
| Admin | admin@velora.test |
| Customer | customer@velora.test |
| Creator | creator@velora.test |
| Supplier | supplier@velora.test |

## 5. Optional Integrations

Configure in `backend/.env` only — never expose to React:

```env
# Payments
STRIPE_KEY=pk_test_...
STRIPE_SECRET=sk_test_...

# Media uploads
CLOUDINARY_CLOUD_NAME=...
CLOUDINARY_API_KEY=...
CLOUDINARY_API_SECRET=...

# AI stylist
OPENAI_API_KEY=sk-...
OPENAI_MODEL=gpt-4o-mini

# Weather context (optional)
WEATHER_API_KEY=...
```

## 6. Queue Worker (Recommended)

```bash
cd backend
php artisan queue:work --tries=3
```

## 7. Verify Installation

```bash
# Backend tests
cd backend && php artisan test

# Release validation
php artisan velora:validate-release --migrate

# Frontend
cd ../frontend
npm run lint
npm run build
```

Expected: 51 tests pass, lint clean, build succeeds.

## 8. Docker (Alternative)

```bash
docker compose up -d
```

See [DEPLOYMENT.md](DEPLOYMENT.md) for production Docker configuration.

## Troubleshooting

| Issue | Solution |
|-------|----------|
| `SQLSTATE[HY000] [1049]` | Create the `velora` database |
| CORS errors | Set `FRONTEND_URL` in backend `.env` |
| 419 CSRF on login | Ensure Sanctum CSRF cookie flow or use token auth |
| AI features unavailable | Set `OPENAI_API_KEY` in backend `.env` |
| Empty catalog | Run `php artisan db:seed` or enable demo mode |

## Next Steps

- [User Guide](USER_GUIDE.md) — Customer features
- [Demo Script](DEMO_SCRIPT.md) — Presentation walkthrough
- [Deployment](DEPLOYMENT.md) — Production setup
