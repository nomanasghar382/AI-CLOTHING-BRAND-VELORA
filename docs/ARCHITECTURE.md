# VELORA Architecture

See [docs/release/ARCHITECTURE.md](release/ARCHITECTURE.md) for the complete architecture documentation including system diagrams.

## System Diagrams

Full Mermaid diagrams available in [diagrams/ARCHITECTURE_DIAGRAMS.md](diagrams/ARCHITECTURE_DIAGRAMS.md):

- Overall Architecture
- Frontend Flow
- Backend Flow
- Authentication Flow
- AI Recommendation Flow
- Wardrobe Flow
- Creator Commerce Flow
- International Commerce Flow
- Database ER Diagram
- Admin Architecture
- Deployment Architecture

## Overview

VELORA is a full-stack modest fashion commerce platform:

```
React PWA  →  Laravel REST API (v1/v2)  →  MySQL
                    ↓
              Redis (cache/queue)
                    ↓
         Stripe · Cloudinary · OpenAI
```

## Frontend

- React 19 with React Router for SPA navigation
- Bootstrap 5 with custom VELORA design system (Atelier theme)
- Framer Motion with reduced-motion support
- Vite build with PWA service worker and code splitting
- Axios API client with retry and offline detection

## Backend

- Laravel 12 REST API with Sanctum authentication
- Service layer pattern for business logic
- Eloquent ORM with eager loading and soft deletes
- Queue workers for email, webhooks, backups, report exports
- Scheduler with 14 automated maintenance tasks

## Security

- Role-based access control with granular permissions
- Security headers middleware (CSP, HSTS)
- Webhook signature verification
- Audit logging for admin actions
- Upload validation middleware

## Integrations

| Service | Purpose | Configuration |
|---------|---------|---------------|
| Stripe | Payments | `STRIPE_*` in `.env` |
| Cloudinary | Media uploads | `CLOUDINARY_*` in `.env` |
| OpenAI | AI stylist | `OPENAI_*` in `.env` |

All integration secrets remain server-side. The React frontend never receives API keys.
