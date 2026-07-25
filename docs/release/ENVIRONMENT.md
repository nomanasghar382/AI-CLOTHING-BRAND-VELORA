# Environment Variables Guide

All secrets are read from Laravel `backend/.env`. Never expose these to the React frontend.

## Application

| Variable | Description |
|----------|-------------|
| `APP_NAME` | Application name (VELORA) |
| `APP_ENV` | `local`, `staging`, `production` |
| `APP_KEY` | Encryption key |
| `APP_DEBUG` | `false` in production |
| `APP_URL` | Backend URL |
| `FRONTEND_URL` | React app URL for CORS and emails |

## Database

| Variable | Description |
|----------|-------------|
| `DB_CONNECTION` | `mysql` |
| `DB_HOST` | Database host |
| `DB_PORT` | Database port |
| `DB_DATABASE` | Database name |
| `DB_USERNAME` | Database user |
| `DB_PASSWORD` | Database password |

## Integrations (Server-Side Only)

| Variable | Description |
|----------|-------------|
| `STRIPE_KEY` | Stripe publishable key (safe for server use only) |
| `STRIPE_SECRET` | Stripe secret key |
| `STRIPE_WEBHOOK_SECRET` | Stripe webhook signing secret |
| `CLOUDINARY_CLOUD_NAME` | Cloudinary cloud name |
| `CLOUDINARY_API_KEY` | Cloudinary API key |
| `CLOUDINARY_API_SECRET` | Cloudinary API secret |
| `OPENAI_API_KEY` | OpenAI API key for style engine |
| `WEATHER_API_KEY` | Optional weather context for recommendations |

## VELORA Configuration

| Variable | Default | Description |
|----------|---------|-------------|
| `VELORA_DEMO_MODE` | `false` | Seed demo environment data |
| `VELORA_RELEASE_VERSION` | `1.0.0-rc.1` | Release version label |
| `VELORA_SLOW_REQUEST_MS` | `750` | Slow request threshold |
| `VELORA_SLOW_QUERY_MS` | `500` | Slow query threshold |
| `VELORA_BACKUP_DISK` | `local` | Backup storage disk |
| `VELORA_BACKUP_RETENTION_DAYS` | `14` | Backup retention |
| `VELORA_MAX_CONCURRENT_SESSIONS` | `5` | Session limit per user |
| `VELORA_PASSWORD_HISTORY_COUNT` | `5` | Password reuse prevention |

## Frontend (Public Only)

| Variable | Description |
|----------|-------------|
| `VITE_API_BASE_URL` | API base URL (no secrets) |
| `VITE_API_TIMEOUT` | Request timeout in ms |
