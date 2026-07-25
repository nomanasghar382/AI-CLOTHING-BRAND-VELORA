# VELORA

**Style, Intelligently Yours.** — Production release v1.0.0

AI-powered global modest fashion commerce platform with enterprise admin, creator commerce, community, loyalty, and ethical fashion passport.

## Stack

| Layer | Technology |
| --- | --- |
| Frontend | React 19, Vite, Bootstrap 5, Framer Motion, PWA |
| Backend | Laravel 12, Sanctum, REST API |
| Database | MySQL 8 |
| Media | Cloudinary (server-side only) |
| Cache / Queue | Redis-ready (file/database drivers supported locally) |

## Quick Start

### Backend

```bash
cd backend
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

### Frontend

```bash
cd frontend
npm install
npm run dev
```

Frontend: `http://localhost:5173`  
API: `http://localhost:8000/api/v1`

## Demo Presentation

```bash
# In backend/.env set VELORA_DEMO_MODE=true
php artisan migrate:fresh --seed
```

| Email | Role | Password |
| --- | --- | --- |
| admin@velora.test | Super Admin | VeloraDemo!2026 |
| customer@velora.test | Customer | VeloraDemo!2026 |
| creator@velora.test | Creator | VeloraDemo!2026 |
| supplier@velora.test | Supplier | VeloraDemo!2026 |

See [Demo Package Guide](docs/DEMO.md) for demonstration workflows.

## Release Certification

```bash
cd backend && php artisan test
cd backend && php artisan velora:validate-release --migrate
cd frontend && npm run lint && npm run build
```

## Documentation

| Guide | Description |
| --- | --- |
| [User Guide](docs/USER_GUIDE.md) | Customer-facing features |
| [Admin Guide](docs/ADMIN_GUIDE.md) | Enterprise admin panel |
| [Creator Guide](docs/CREATOR_GUIDE.md) | Creator commerce tools |
| [Supplier Guide](docs/SUPPLIER_GUIDE.md) | Supplier operations portal |
| [API Reference](docs/API.md) | REST API endpoints |
| [Architecture](docs/ARCHITECTURE.md) | System design |
| [Database](docs/DATABASE.md) | Schema and seeding |
| [Security](docs/SECURITY.md) | Security practices |
| [Deployment](docs/DEPLOYMENT.md) | Production deployment |
| [Developer Guide](docs/DEVELOPER.md) | Development setup |
| [Testing](docs/TESTING.md) | Test suites and validation |
| [Maintenance](docs/MAINTENANCE.md) | Operations and maintenance |
| [Project Structure](docs/PROJECT_STRUCTURE.md) | Codebase layout |
| [Enterprise](docs/ENTERPRISE.md) | Enterprise infrastructure |
| [Release Notes](RELEASE_NOTES.md) | v1.0.0 release notes |
| [Changelog](CHANGELOG.md) | Version history |

## License

Proprietary — VELORA platform source.
