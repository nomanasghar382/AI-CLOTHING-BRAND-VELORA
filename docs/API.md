# API Overview

Base URL: `/api/v1`

All responses use:

```json
{
  "success": true,
  "message": "Optional message",
  "data": {},
  "errors": {},
  "status": 200,
  "timestamp": "2026-07-25T12:00:00+00:00"
}
```

## Public catalog

| Method | Endpoint | Description |
| --- | --- | --- |
| GET | `/products` | Paginated catalog with filters |
| GET | `/products/{slug}` | Product detail |
| GET | `/categories` | Category tree |
| GET | `/catalog/filters` | Dynamic filter metadata |
| GET | `/search` | Instant search |
| GET | `/search/suggestions` | Suggestions, popular and recent |

## Authentication

| Method | Endpoint | Description |
| --- | --- | --- |
| POST | `/auth/register` | Register |
| POST | `/auth/login` | Login |
| GET | `/auth/me` | Current user (auth) |
| POST | `/auth/logout` | Logout (auth) |

## Shopping (auth)

| Method | Endpoint | Description |
| --- | --- | --- |
| GET | `/cart` | Cart snapshot |
| POST | `/cart/items` | Add item |
| GET | `/wishlist` | Wishlist |
| POST | `/checkout` | Checkout |
| GET | `/orders` | Order history |

## Notifications (auth)

| Method | Endpoint | Description |
| --- | --- | --- |
| GET | `/notifications` | User notifications |
| PATCH | `/notifications/{id}` | Mark read |
| POST | `/notifications/read-all` | Mark all read |

## Loyalty (auth)

| Method | Endpoint | Description |
| --- | --- | --- |
| GET | `/loyalty/overview` | Points and tier |
| GET | `/loyalty/rewards` | Reward catalog |
| GET | `/loyalty/wallet` | Wallet and transactions |
| GET | `/loyalty/achievements` | Achievements |

## Admin (auth + role)

| Method | Endpoint | Description |
| --- | --- | --- |
| GET | `/admin/system/health` | Application health |
| POST | `/admin/system/cache/clear` | Clear caches |
| GET | `/admin/analytics` | Business intelligence |
| GET | `/admin/orders` | Order management |

## Headers

| Header | Purpose |
| --- | --- |
| `Authorization: Bearer {token}` | Sanctum access token |
| `X-Session-Id` | Anonymous search/session correlation |
| `X-Response-Time` | Request duration (added by API middleware) |

## Error codes

| Status | Meaning |
| --- | --- |
| 401 | Unauthenticated |
| 403 | Forbidden |
| 404 | Not found |
| 419 | Session expired |
| 422 | Validation error |
| 429 | Rate limited |
| 500 | Server error |
| 503 | Maintenance |
