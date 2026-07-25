# Enterprise Infrastructure

VELORA enterprise infrastructure covers security hardening, audit logging, observability, structured logging, queues, scheduler automation, cache architecture, file management, backups, webhooks, API versioning, Docker, and CI/CD.

## Security

- Security headers middleware: CSP, HSTS, XSS protection, clickjacking protection, referrer policy, permissions policy
- Strict CORS with explicit allowed headers
- Brute-force protection and suspicious login detection
- Device registration, trusted browsers, concurrent session limits
- Password rotation with history enforcement
- Upload validation and virus-scan hook architecture

## Audit system

`audit_logs` tracks authentication, orders, payments, inventory, admin, supplier, creator, AI, configuration, permissions, exports, and deletions via `AuditService`.

## Observability

- `MetricsService` snapshots database, cache, queue, memory, storage, and error metrics
- Slow query logging via `LogSlowQueries` middleware
- `application_metrics` table for historical metric retention

## Logging channels

Dedicated daily channels: `auth`, `payments`, `orders`, `ai`, `security`, `uploads`, `notifications`, `email`, `queues`, `scheduler`, `webhooks`, `performance`.

## Queues

Heavy work is queued through jobs:

- `SendTransactionalEmailJob`
- `ProcessIncomingWebhookJob`
- `GenerateReportExportJob`
- `RefreshRecommendationsJob`
- `RunDatabaseBackupJob`
- `ProcessCloudinaryUploadJob`
- `SyncSupplierInventory` (existing)

## Scheduler

Scheduled via `routes/console.php` using `velora:scheduler-run {task}` with `scheduled_task_runs` observability.

## Cache

`CacheManagerService` provides tagged cache support (Redis-ready), invalidation, and warmup routines.

## Backups

`BackupService` handles database and configuration backups with retention policy and `backup_runs` tracking.

## Webhooks

Inbound providers:

- `POST /api/v1/webhooks/stripe`
- `POST /api/v1/webhooks/cloudinary`
- `POST /api/v1/webhooks/shipping`

Events are verified, logged, and processed asynchronously.

## API versioning

- Active API: `/api/v1`
- Architecture endpoint: `/api/v2/status`
- Version middleware: `X-Api-Version` header with deprecation support

## Admin operations

`/admin/operations/*` endpoints expose metrics, queues, scheduler runs, audits, security events, backups, feature flags, webhooks, maintenance mode, and cache management.

## Docker

```bash
docker compose up --build
```

Services: nginx, php-fpm, queue worker, scheduler, MySQL, Redis, frontend dev server.

## CI/CD

GitHub Actions workflow `.github/workflows/ci.yml` runs Laravel tests, React build, lint, and security scans.
