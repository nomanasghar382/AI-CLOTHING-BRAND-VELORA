# Changelog

All notable changes to VELORA are documented in this file.

## [1.0.0] - 2026-07-25

### Release
- VELORA v1.0.0 — production release of the full-stack modest fashion commerce platform
- Complete documentation suite (45+ guides + release checklists)
- Release validation command with migration support (`velora:validate-release --migrate`)
- Release certification test suite (51 tests, 349 assertions)
- Demo package with `VELORA_DEMO_MODE` for semester presentation

### Submission Package
- Project overview, feature matrix, and project statistics
- 10 Mermaid architecture diagrams
- 15–20 minute demo script and presentation outline
- Portfolio package (resume, LinkedIn, GitHub, case study)
- Submission verification script (`scripts/verify-submission.sh`)
- LICENSE and CONTRIBUTING guidelines

### Fixed
- Release validation auto-migrates pending schema when migrations table exists
- Admin command palette utility exports moved to `adminPreferences.js`

## [1.0.0-rc.2] - 2026-07-25

### Fixed
- Webhook endpoints now reject unverified signatures with 401
- Gift card redemption returns validation errors instead of 404 for invalid codes
- Cart and wishlist no longer flash empty state while loading
- API client error messages now surface offline and timeout states
- Product alert dispatch N+1 query on notification preferences

### Enhanced
- Shared `getApiErrorMessage`, `formatMoney`, `useAsyncAction`, and reduced-motion hooks
- Error states support retry across catalog, orders, and product detail pages
- Community, navbar, and pagination accessibility improvements
- Admin dashboard recent orders return slim response payloads
- Demo seeder expanded with 8 orders, 14-day analytics, and resolved support ticket
- International page discloses locally estimated shipping and duty fallbacks

### Added
- Gift card and webhook signature regression tests (47 tests, 324 assertions)

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
