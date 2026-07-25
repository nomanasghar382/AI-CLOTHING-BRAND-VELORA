# VELORA

**Style, Intelligently Yours.** — Production release v1.0.0

AI-powered global modest fashion commerce platform with enterprise admin, creator commerce, community, loyalty, and ethical fashion passport.

[![Version](https://img.shields.io/badge/version-1.0.0-gold)](VERSION)
[![Tests](https://img.shields.io/badge/tests-51%20passed-brightgreen)](docs/TESTING.md)
[![API](https://img.shields.io/badge/API-198%20endpoints-blue)](docs/API_REFERENCE.md)

## Stack

| Layer | Technology |
| --- | --- |
| Frontend | React 19, Vite, Bootstrap 5, Framer Motion, PWA |
| Backend | Laravel 12, Sanctum, REST API |
| Database | MySQL 8 (144 tables) |
| Media | Cloudinary (server-side only) |
| AI | OpenAI GPT (server-side only) |
| Cache / Queue | Redis-ready (file/database drivers supported locally) |

## Quick Start

See [Installation Guide](docs/INSTALLATION.md) for detailed setup.

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

See [Demo Script](docs/DEMO_SCRIPT.md) for a 15–20 minute presentation guide.

## Release Certification

```bash
cd backend && php artisan test
cd backend && php artisan velora:validate-release --migrate
cd frontend && npm run lint && npm run build
bash scripts/verify-submission.sh
```

## Documentation

### Project

| Guide | Description |
| --- | --- |
| [Project Overview](docs/PROJECT_OVERVIEW.md) | Executive summary and module index |
| [Architecture](docs/ARCHITECTURE.md) | System design and patterns |
| [Architecture Diagrams](docs/diagrams/ARCHITECTURE_DIAGRAMS.md) | Mermaid system diagrams |
| [Feature Matrix](docs/FEATURE_MATRIX.md) | Module coverage and role access |
| [Project Statistics](docs/PROJECT_STATISTICS.md) | Automated codebase metrics |
| [Release Summary](docs/RELEASE_SUMMARY.md) | Final release certification |

### Technical

| Guide | Description |
| --- | --- |
| [API Reference](docs/API_REFERENCE.md) | REST API endpoints |
| [Database](docs/DATABASE.md) | Schema and seeding |
| [AI Engine](docs/AI_ENGINE.md) | AI fashion engine |
| [Security](docs/SECURITY.md) | Security practices |
| [Deployment](docs/DEPLOYMENT.md) | Production deployment |
| [Installation](docs/INSTALLATION.md) | Step-by-step setup |
| [Testing](docs/TESTING.md) | Test suites and validation |
| [Developer Guide](docs/DEVELOPER.md) | Development setup |
| [Maintenance](docs/MAINTENANCE.md) | Operations and maintenance |
| [Project Structure](docs/PROJECT_STRUCTURE.md) | Codebase layout |
| [Enterprise](docs/ENTERPRISE.md) | Enterprise infrastructure |

### User Guides

| Guide | Description |
| --- | --- |
| [User Guide](docs/USER_GUIDE.md) | Customer-facing features |
| [Admin Guide](docs/ADMIN_GUIDE.md) | Enterprise admin panel |
| [Creator Guide](docs/CREATOR_GUIDE.md) | Creator commerce tools |
| [Supplier Guide](docs/SUPPLIER_GUIDE.md) | Supplier operations portal |
| [Demo Package](docs/DEMO.md) | Demo environment guide |

### Submission & Portfolio

| Guide | Description |
| --- | --- |
| [Demo Script](docs/DEMO_SCRIPT.md) | 15–20 minute demonstration guide |
| [Presentation](docs/PRESENTATION.md) | Slide deck outline |
| [Verification](docs/VERIFICATION.md) | Submission checklist |
| [Portfolio Package](docs/portfolio/PORTFOLIO.md) | Resume, LinkedIn, case study |
| [Contributing](CONTRIBUTING.md) | Contribution guidelines |
| [Release Notes](RELEASE_NOTES.md) | v1.0.0 release notes |
| [Changelog](CHANGELOG.md) | Version history |

## License

See [LICENSE](LICENSE).
