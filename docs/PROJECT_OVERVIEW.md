# VELORA — Project Overview

**Version:** 1.0.0  
**Tagline:** Style, Intelligently Yours.

## Executive Summary

VELORA is a production-grade, AI-powered global modest fashion commerce platform built as a full-stack capstone project. It combines a React progressive web application with a Laravel REST API to deliver end-to-end shopping, AI styling, creator commerce, community engagement, supplier operations, and enterprise administration.

The platform targets modest fashion consumers worldwide, with emphasis on ethical sourcing transparency, international shipping, and personalized AI recommendations grounded in real catalog inventory.

## Problem Statement

Modest fashion shoppers face fragmented experiences: limited size guidance across regions, poor outfit coordination tools, opaque sustainability claims, and disconnected creator ecosystems. Traditional e-commerce platforms treat fashion as transactional catalog browsing rather than personal style discovery.

## Solution

VELORA unifies commerce and intelligence in a single platform:

- **Shop** — Catalog, cart, checkout, orders, returns, and international commerce
- **Discover** — AI style profile, occasion stylist, outfit builder, and visual search
- **Organize** — Digital wardrobe and saved outfits
- **Connect** — Creator collections, lookboards, and community posts
- **Trust** — Ethical fashion passport with verified sustainability evidence
- **Reward** — Loyalty wallet, VIP tiers, referrals, and gift cards
- **Operate** — Supplier portal, inventory sync, and enterprise admin with BI

## Technology Stack

| Layer | Technology |
|-------|------------|
| Frontend | React 19, Vite, Bootstrap 5, Framer Motion, PWA |
| Backend | Laravel 12, Sanctum, REST API (v1/v2) |
| Database | MySQL 8 (144 tables) |
| Media | Cloudinary (server-side uploads only) |
| Payments | Stripe |
| AI | OpenAI GPT (server-side, catalog-grounded) |
| Cache / Queue | Redis-ready (database drivers for local dev) |
| DevOps | Docker, GitHub Actions CI/CD |

## Architecture Summary

```
┌─────────────────┐     HTTPS/JSON      ┌──────────────────┐
│  React PWA      │ ◄─────────────────► │  Laravel API     │
│  (Bootstrap 5)  │     Sanctum tokens  │  (REST v1/v2)    │
└─────────────────┘                     └────────┬─────────┘
                                                 │
                    ┌────────────────────────────┼────────────────────────────┐
                    ▼                            ▼                            ▼
              ┌──────────┐               ┌──────────┐               ┌──────────┐
              │  MySQL   │               │  Redis   │               │ External │
              │  144 tbl │               │  cache/  │               │ Stripe   │
              └──────────┘               │  queue   │               │ Cloudinary│
                                         └──────────┘               │ OpenAI   │
                                                                    └──────────┘
```

See [ARCHITECTURE.md](ARCHITECTURE.md) and [diagrams/ARCHITECTURE_DIAGRAMS.md](diagrams/ARCHITECTURE_DIAGRAMS.md) for detailed system diagrams.

## Feature Modules

| # | Module | Description |
|---|--------|-------------|
| 1 | Authentication | Registration, login, roles, permissions, device trust |
| 2 | Product Catalog | Categories, filters, instant search, SEO |
| 3 | Shopping Cart | Cart, wishlist, coupons, checkout |
| 4 | Orders & Returns | Order tracking, invoices, return requests |
| 5 | AI Style Profile | Style quiz, body profile, preference engine |
| 6 | AI Occasion Stylist | Occasion-based outfit generation |
| 7 | AI Outfit Builder | Recommendation engine with saved outfits |
| 8 | AI Wardrobe | Digital closet management |
| 9 | Visual Search | Image-based product discovery |
| 10 | Creator Commerce | Collections, commissions, withdrawals |
| 11 | Community | Posts, comments, likes, bookmarks, moderation |
| 12 | International Commerce | Multi-currency, shipping, duty estimates |
| 13 | Ethical Fashion Passport | Verified sustainability claims |
| 14 | Loyalty Platform | Points, tiers, referrals, gift cards |
| 15 | Supplier Portal | Inventory, POs, settlements, sync |
| 16 | Business Intelligence | Analytics, forecasts, dashboards |
| 17 | Enterprise Admin | Command palette, global search, operations |
| 18 | PWA | Offline support, install prompt, service worker |

Full matrix: [FEATURE_MATRIX.md](FEATURE_MATRIX.md)

## Project Statistics

| Metric | Count |
|--------|------:|
| Feature modules | 18 |
| Database tables | 144 |
| API endpoints | 198 |
| React pages | 31 |
| Reusable components | 44 |
| Laravel models | 101 |
| Services | 27 |
| Automated tests | 51 |

Full statistics: [PROJECT_STATISTICS.md](PROJECT_STATISTICS.md)

## Target Users

| Role | Portal | Key Actions |
|------|--------|-------------|
| Guest | Storefront | Browse catalog, search, view products |
| Customer | Storefront + AI | Shop, style, wardrobe, community, loyalty |
| Creator | Creator tools | Collections, lookboards, earnings |
| Supplier | Supplier portal | Inventory, shipments, settlements |
| Administrator | Admin panel | Operations, analytics, content, support |

## Demo Environment

```bash
# backend/.env
VELORA_DEMO_MODE=true

php artisan migrate:fresh --seed
```

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@velora.test | VeloraDemo!2026 |
| Customer | customer@velora.test | VeloraDemo!2026 |
| Creator | creator@velora.test | VeloraDemo!2026 |
| Supplier | supplier@velora.test | VeloraDemo!2026 |

## Documentation Index

| Document | Purpose |
|----------|---------|
| [INSTALLATION.md](INSTALLATION.md) | Step-by-step setup |
| [ARCHITECTURE.md](ARCHITECTURE.md) | System design |
| [API_REFERENCE.md](API_REFERENCE.md) | REST API endpoints |
| [DATABASE.md](DATABASE.md) | Schema and relationships |
| [AI_ENGINE.md](AI_ENGINE.md) | AI fashion engine |
| [DEPLOYMENT.md](DEPLOYMENT.md) | Production deployment |
| [SECURITY.md](SECURITY.md) | Security practices |
| [DEMO_SCRIPT.md](DEMO_SCRIPT.md) | 15–20 minute demo guide |
| [PRESENTATION.md](PRESENTATION.md) | Presentation outline |
| [RELEASE_SUMMARY.md](RELEASE_SUMMARY.md) | Final release summary |
| [portfolio/PORTFOLIO.md](portfolio/PORTFOLIO.md) | Portfolio package |

## Academic Context

VELORA demonstrates mastery of:

- Full-stack web development (React + Laravel)
- REST API design with authentication and authorization
- Relational database modeling (144 tables, normalized schema)
- AI integration with catalog-grounded recommendations
- Progressive Web App development
- Enterprise patterns (repositories, services, policies, queues)
- Security best practices (Sanctum, RBAC, audit logging)
- DevOps readiness (Docker, CI/CD, health monitoring)
