# VELORA Security Guide

## Authentication

- Laravel Sanctum bearer tokens for API authentication
- Password hashing with bcrypt (12 rounds)
- Password history prevents reuse of recent passwords
- Concurrent session limits per user account
- Email verification required for new accounts

## Authorization

- Role-based access: `super-admin`, `admin`, `customer`, `creator`, `supplier`
- Permission middleware on all admin and supplier endpoints
- Policy checks on resource ownership (orders, wardrobe, community posts)

## Transport & Headers

- `SecurityHeaders` middleware sets CSP, HSTS, X-Frame-Options, X-Content-Type-Options
- CORS restricted to `FRONTEND_URL`
- Rate limiting on API, auth, uploads, and webhooks

## Secrets Management

All secrets are read exclusively from `backend/.env`:

- `STRIPE_SECRET`, `STRIPE_WEBHOOK_SECRET`
- `CLOUDINARY_API_SECRET`, `CLOUDINARY_WEBHOOK_SECRET`
- `OPENAI_API_KEY`
- `SHIPPING_WEBHOOK_SECRET`

Never expose secrets to the React frontend. The frontend only receives `VITE_API_BASE_URL`.

## Upload Security

- `ValidateUpload` middleware enforces MIME type and size limits
- Allowed types: JPEG, PNG, WebP, GIF
- Maximum size: `VELORA_UPLOAD_MAX_KB` (default 10 MB)

## Audit & Monitoring

- `AuditService` records security-sensitive admin actions
- `SecurityLoginEvent` tracks login attempts and device fingerprints
- Suspicious login threshold alerts via `VELORA_SUSPICIOUS_LOGIN_THRESHOLD`

## Webhooks

- Stripe signature verification via HMAC-SHA256
- Cloudinary and shipping webhooks verified via shared secret
- Unverified webhooks rejected with HTTP 401

## Session Handling

- Sanctum token revocation on logout
- Session expiration returns HTTP 419 with redirect to `/session-expired`
- Unauthorized access returns HTTP 401

## Production Checklist

See [docs/release/SECURITY_CHECKLIST.md](release/SECURITY_CHECKLIST.md) for deployment security verification.
