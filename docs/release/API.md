# VELORA API Documentation (Release Candidate)

Base URL: `/api/v1`

All responses use the envelope:

```json
{ "success": true, "message": "...", "data": { } }
```

## Authentication

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/auth/register` | Register customer account |
| POST | `/auth/login` | Login and receive Sanctum token |
| POST | `/auth/logout` | Revoke current token |
| GET | `/auth/me` | Current user profile |
| PUT | `/auth/profile` | Update profile |
| PUT | `/auth/password` | Change password |

Authorization header: `Bearer {token}`

## Catalog

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/products` | Paginated product catalog |
| GET | `/products/{slug}` | Product detail |
| GET | `/categories` | Category tree |
| GET | `/catalog/filters` | Filter metadata |
| GET | `/search` | Full-text search |
| GET | `/search/suggestions` | Instant search suggestions |

## Shopping

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/cart` | Current cart |
| POST | `/cart/items` | Add item |
| PATCH | `/cart/items/{id}` | Update quantity |
| DELETE | `/cart/items/{id}` | Remove item |
| POST | `/checkout` | Create order |
| GET | `/orders` | User orders |
| GET | `/orders/{id}` | Order detail |
| POST | `/orders/{id}/payment-intent` | Stripe payment intent |
| POST | `/orders/{id}/returns` | Request return |

## AI & Style

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/style/quiz` | Style quiz |
| GET | `/style/profile` | AI style profile |
| POST | `/style/recommendations` | Generate recommendations |
| POST | `/style/chat` | Style assistant chat |
| GET | `/style/trends` | Fashion trends |

## Creator & Community

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/creator/profile` | Creator profile |
| PUT | `/creator/profile` | Update creator profile |
| GET | `/lookboards` | User lookboards |
| POST | `/lookboards` | Create lookboard |
| GET | `/wardrobe` | Wardrobe items |
| POST | `/wardrobe` | Add wardrobe item |
| POST | `/visual-searches` | Visual search |
| GET | `/community/posts` | Community feed |
| POST | `/community/posts` | Create post |

## Loyalty & Passport

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/loyalty/wallet` | Points and credit balance |
| GET | `/loyalty/rewards` | Redeemable rewards |
| GET | `/products/{id}/ethical-passport` | Product passport |

## Admin

| Method | Endpoint | Permission |
|--------|----------|------------|
| GET | `/admin/dashboard` | dashboard.view |
| GET | `/admin/workspace` | dashboard.view |
| GET | `/admin/search?q=` | content.view |
| GET | `/admin/analytics` | dashboard.view |
| GET | `/admin/orders` | orders.manage |
| GET | `/admin/products` | catalog.manage |
| GET | `/admin/system/health` | super-admin, admin |
| GET | `/admin/operations/*` | super-admin, admin |

## Webhooks

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/v1/webhooks/stripe` | Stripe events |
| POST | `/v1/webhooks/cloudinary` | Cloudinary events |
| POST | `/v1/webhooks/shipping` | Shipping carrier events |

See also `docs/API.md` for developer-oriented endpoint notes.
