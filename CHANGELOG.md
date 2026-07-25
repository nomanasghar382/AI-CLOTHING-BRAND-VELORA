# Changelog

All notable changes to VELORA are documented in this file.

## [1.0.0-rc.1] - 2026-07-25

### Added
- Release Candidate validation command (`php artisan velora:validate-release`)
- Demo environment seeder (`DemoEnvironmentSeeder`) controlled by `VELORA_DEMO_MODE`
- Admin global search API (`GET /api/v1/admin/search`)
- Admin workspace API with quick actions, shortcuts, pinned dashboards, and recent activity
- Admin command palette with keyboard shortcut (⌘/Ctrl+K)
- API client retry with exponential backoff for network and 5xx errors
- Release workflow integration tests
- Complete release documentation suite under `docs/release/`

### Enhanced
- Admin dashboard with quick actions, pinned dashboards, and recent activity feed
- Admin topbar search wired to command palette
- Dashboard API includes release metadata

### Security
- Secrets remain server-side in Laravel `.env` only
- Admin search and workspace endpoints protected by Sanctum and permissions

## [0.11.0] - 2026-07-25

### Added
- Enterprise security, observability, queues, scheduler, backups, and webhooks
- Docker Compose, GitHub Actions CI, API versioning

## [0.10.0] - 2026-07-25

### Added
- PWA, instant search, advanced filters, toast notifications, SEO, accessibility improvements

## [0.9.0] - 2026-07-25

### Added
- Supplier intelligence and forecasting platform
