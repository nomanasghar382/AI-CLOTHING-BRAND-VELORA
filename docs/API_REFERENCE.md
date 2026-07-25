# VELORA API Reference

**Version:** 1.0.0  
**Base URL:** `/api/v1`  
**Authentication:** Laravel Sanctum (Bearer token)

## Response Envelope

All API responses follow a consistent envelope:

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

### Error Responses

| Status | Meaning |
|--------|---------|
| 400 | Bad request |
| 401 | Unauthenticated |
| 403 | Forbidden (policy/permission) |
| 404 | Resource not found |
| 422 | Validation failed |
| 429 | Rate limited |
| 500 | Server error |
| 503 | Service unavailable (e.g. AI/media) |

## Authentication

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| POST | `/auth/register` | — | Register new account |
| POST | `/auth/login` | — | Login, returns token |
| POST | `/auth/logout` | ✓ | Revoke current token |
| GET | `/auth/me` | ✓ | Current user profile |
| PUT | `/auth/profile` | ✓ | Update profile |
| PUT | `/auth/password` | ✓ | Change password |
| POST | `/auth/forgot-password` | — | Request reset link |
| POST | `/auth/reset-password` | — | Reset password |
| GET | `/auth/devices` | ✓ | Trusted devices |
| DELETE | `/auth/devices/{id}` | ✓ | Revoke device |
| GET | `/auth/security/activity` | ✓ | Security audit log |
| POST | `/auth/email/verify` | ✓ | Verify email |
| POST | `/auth/email/resend` | ✓ | Resend verification |

## Catalog & Search

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/products` | — | Paginated catalog (filters, sort, search) |
| GET | `/products/{slug}` | — | Product detail |
| GET | `/categories` | — | Category tree |
| GET | `/catalog/filters` | — | Dynamic filter metadata |
| GET | `/search` | — | Instant search |
| GET | `/search/suggestions` | — | Autocomplete suggestions |

**Query parameters (products):** `page`, `per_page`, `category`, `brand`, `min_price`, `max_price`, `color`, `size`, `sort`, `q`

## Shopping

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/cart` | ✓ | Cart snapshot |
| POST | `/cart/items` | ✓ | Add item |
| PUT | `/cart/items/{id}` | ✓ | Update quantity |
| DELETE | `/cart/items/{id}` | ✓ | Remove item |
| GET | `/wishlist` | ✓ | Wishlist items |
| POST | `/wishlist/items` | ✓ | Add to wishlist |
| DELETE | `/wishlist/items/{id}` | ✓ | Remove from wishlist |
| POST | `/checkout` | ✓ | Process checkout |
| GET | `/orders` | ✓ | Order history |
| GET | `/orders/{id}` | ✓ | Order detail |
| POST | `/orders/{id}/cancel` | ✓ | Cancel order |
| POST | `/orders/{id}/returns` | ✓ | Request return |

## AI Style Engine

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/style/profile` | ✓ | Style profile |
| POST | `/style/quiz` | ✓ | Submit style quiz |
| GET | `/style/recommendations` | ✓ | Get recommendations |
| POST | `/style/recommendations` | ✓ | Generate recommendations |
| POST | `/style/outfits/occasion` | ✓ | Occasion outfit builder |
| POST | `/style/chat` | ✓ | Stylist conversation |
| GET | `/style/conversations` | ✓ | Chat history |
| GET | `/style/saved-outfits` | ✓ | Saved outfits |
| POST | `/style/saved-outfits` | ✓ | Save outfit |
| GET | `/style/trends` | — | Fashion trends |
| POST | `/style/feedback` | ✓ | Recommendation feedback |

## Wardrobe & Visual Search

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/wardrobe` | ✓ | Wardrobe items |
| POST | `/wardrobe` | ✓ | Add wardrobe item |
| PUT | `/wardrobe/{id}` | ✓ | Update item |
| DELETE | `/wardrobe/{id}` | ✓ | Remove item |
| POST | `/visual-searches` | ✓ | Upload image search |
| GET | `/visual-searches/{id}` | ✓ | Search results |

## Creator Commerce

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/creator/profile` | ✓ | Creator profile |
| PUT | `/creator/profile` | ✓ | Update profile |
| GET | `/creator/collections` | ✓ | Collections |
| POST | `/creator/collections` | ✓ | Create collection |
| GET | `/creator/earnings` | ✓ | Commission summary |
| POST | `/creator/withdrawals` | ✓ | Request withdrawal |
| GET | `/creators` | — | Public creator directory |
| GET | `/creators/{slug}` | — | Public creator profile |
| GET | `/lookboards` | — | Public lookboards |
| POST | `/lookboards` | ✓ | Create lookboard |

## Community

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/community/posts` | — | Feed (paginated) |
| POST | `/community/posts` | ✓ | Create post |
| GET | `/community/posts/{id}` | — | Post detail |
| PUT | `/community/posts/{id}` | ✓ | Update post |
| DELETE | `/community/posts/{id}` | ✓ | Delete post |
| POST | `/community/posts/{id}/like` | ✓ | Toggle like |
| POST | `/community/posts/{id}/comments` | ✓ | Add comment |
| POST | `/community/posts/{id}/bookmark` | ✓ | Toggle bookmark |
| POST | `/community/posts/{id}/report` | ✓ | Report post |

## International Commerce

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/international/countries` | — | Country list |
| GET | `/international/currencies` | — | Active currencies |
| GET | `/international/shipping-estimate` | — | Shipping estimate |
| GET | `/international/size-guide` | — | Regional size guide |
| POST | `/international/addresses` | ✓ | Save address |
| GET | `/international/addresses` | ✓ | User addresses |
| GET | `/international/tracking/{code}` | — | Shipment tracking |

## Loyalty & Ethical Passport

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/loyalty/overview` | ✓ | Wallet and tier |
| GET | `/loyalty/transactions` | ✓ | Point history |
| GET | `/loyalty/rewards` | ✓ | Available rewards |
| POST | `/loyalty/redeem` | ✓ | Redeem reward |
| GET | `/loyalty/referrals` | ✓ | Referral codes |
| POST | `/loyalty/gift-cards/redeem` | ✓ | Redeem gift card |
| GET | `/loyalty/product-alerts` | ✓ | Price/stock alerts |
| POST | `/loyalty/product-alerts` | ✓ | Create alert |
| GET | `/loyalty/passport` | ✓ | Ethical passport |
| GET | `/loyalty/passport/{productId}` | — | Product passport |

## Supplier Portal

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/supplier/profile` | ✓ | Supplier profile |
| PUT | `/supplier/profile` | ✓ | Update profile |
| GET | `/supplier/inventory` | ✓ | Inventory levels |
| POST | `/supplier/inventory/sync` | ✓ | Trigger sync |
| GET | `/supplier/purchase-orders` | ✓ | Purchase orders |
| GET | `/supplier/shipments` | ✓ | Shipments |
| GET | `/supplier/settlements` | ✓ | Payment settlements |
| POST | `/supplier/documents` | ✓ | Upload document |
| GET | `/supplier/messages` | ✓ | Support messages |
| POST | `/supplier/webhooks` | ✓ | Configure webhook |

## Notifications

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/notifications` | ✓ | User notifications |
| PATCH | `/notifications/{id}` | ✓ | Mark read |
| POST | `/notifications/read-all` | ✓ | Mark all read |

## Admin (74 endpoints)

Admin routes require Sanctum authentication and appropriate permissions.

| Area | Prefix | Key Endpoints |
|------|--------|---------------|
| Dashboard | `/admin/dashboard` | KPIs, recent activity |
| Search | `/admin/search` | Global search |
| Workspace | `/admin/workspace` | Pinned dashboards, shortcuts |
| Customers | `/admin/customers` | CRUD, activity |
| Products | `/admin/products` | Catalog management |
| Orders | `/admin/orders` | Fulfillment, status |
| Coupons | `/admin/coupons` | Discount management |
| Content | `/admin/content` | CMS entries |
| Support | `/admin/support` | Tickets, messages |
| Settings | `/admin/settings` | Platform configuration |
| BI | `/admin/bi` | Analytics, forecasts |
| Operations | `/admin/operations` | Queues, scheduler, backups |
| System | `/admin/system/health` | Health checks |

Full admin reference: [release/API.md](release/API.md)

## Webhooks

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/webhooks/stripe` | Stripe payment events |
| POST | `/webhooks/cloudinary` | Media processing events |
| POST | `/webhooks/shipping` | Carrier tracking updates |

All webhooks verify signatures before processing.

## API Versioning

| Version | Status | Base |
|---------|--------|------|
| v1 | Active (primary) | `/api/v1` |
| v2 | Reserved | `/api/v2/status` |

Version negotiation via `Accept-Version` header or URL prefix.

## Rate Limiting

- Auth routes: 10 requests/minute
- General API: 60 requests/minute
- Admin routes: 120 requests/minute

## Pagination

List endpoints return:

```json
{
  "data": [...],
  "meta": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 15,
    "total": 72
  },
  "links": {
    "first": "...",
    "last": "...",
    "prev": null,
    "next": "..."
  }
}
```

Query: `?page=1&per_page=15`

## Related Documentation

- [API.md](API.md) — Quick reference
- [release/API.md](release/API.md) — Complete admin API
- [SECURITY.md](SECURITY.md) — Authentication and authorization
- [AI_ENGINE.md](AI_ENGINE.md) — AI endpoint details
