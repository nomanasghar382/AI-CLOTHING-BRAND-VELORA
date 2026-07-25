# VELORA Project Statistics

**Version:** 1.0.0  
**Generated:** 2026-07-25  
**Method:** Automated codebase analysis

## Summary

| Category | Count |
|----------|------:|
| **Feature modules** | 24 |
| **Database tables** | 144 |
| **Migration files** | 21 |
| **API endpoints** | 198 |
| **React pages** | 31 |
| **Reusable components** | 44 |
| **React hooks** | 12 |
| **React contexts** | 8 |
| **React utilities** | 5 |
| **Laravel controllers** | 24 |
| **Repositories** | 6 (+ 6 interfaces) |
| **Services** | 27 |
| **Models** | 101 |
| **Policies** | 8 |
| **Form requests** | 26 |
| **API resources** | 18 |
| **Middleware** | 7 |
| **Queue jobs** | 7 |
| **Artisan commands** | 3 |
| **Events** | 1 |
| **Listeners** | 2 |
| **Notifications** | 2 |
| **Seeders** | 12 |
| **Factories** | 7 |
| **PHPUnit test files** | 23 |
| **Test methods** | 51 |
| **Documentation files** | 30+ |

## Database

| Metric | Count |
|--------|------:|
| Migration files | 21 |
| Tables created | 144 |
| Tables altered | 3 (`users`, `products`, `orders`) |
| Seeders | 12 |
| Factories | 7 |

### Migration Timeline

| Migration | Domain |
|-----------|--------|
| `0001_01_01_000000` | Users, sessions, password resets |
| `0001_01_01_000001` | Cache |
| `0001_01_01_000002` | Jobs, failed jobs |
| `2026_07_25_101647–101722` | RBAC (roles, permissions) |
| `2026_07_25_104606` | Catalog (products, categories, brands) |
| `2026_07_25_110000` | Shopping (cart, orders, payments) |
| `2026_07_25_120000` | Tax rules, returns |
| `2026_07_25_120605` | Search, production tables |
| `2026_07_25_130000` | AI fashion engine |
| `2026_07_25_140000` | Admin domain |
| `2026_07_25_150000` | Creator, community, wardrobe, visual search |
| `2026_07_25_160000` | International commerce |
| `2026_07_25_170000` | Loyalty, ethical passport |
| `2026_07_25_180000` | Supplier operations, BI |
| `2026_07_25_190000` | Enterprise infrastructure |

## API Endpoints

| Prefix | Routes |
|--------|-------:|
| `api/v1/admin` | 74 |
| `api/v1/auth` | 15 |
| `api/v1/community` | 14 |
| `api/v1/loyalty` | 13 |
| `api/v1/style` | 13 |
| `api/v1/supplier` | 11 |
| `api/v1/international` | 7 |
| `api/v1/creator` | 7 |
| `api/v1/orders` | 5 |
| `api/v1/wardrobe` | 5 |
| `api/v1/cart` | 4 |
| `api/v1/creators` | 4 |
| `api/v1/lookboards` | 4 |
| `api/v1/products` | 3 |
| `api/v1/notifications` | 3 |
| `api/v1/webhooks` | 3 |
| `api/v1/wishlist` | 3 |
| `api/v1/search` | 2 |
| `api/v1/visual-searches` | 2 |
| Other v1 | 5 |
| `api/v2/status` | 1 |
| **Total** | **198** |

## Frontend

### Pages (31)

| Folder | Count |
|--------|------:|
| `ai/` | 8 |
| `shopping/` | 6 |
| `community/` | 3 |
| `admin/` | 2 |
| `auth/` | 2 |
| `catalog/` | 2 |
| `errors/` | 2 |
| Root | 2 |
| `loyalty/` | 1 |
| `search/` | 1 |
| `supplier/` | 1 |
| `wardrobe/` | 1 |

### Components (44)

| Folder | Count |
|--------|------:|
| `common/` | 8 |
| `admin/` | 7 |
| `system/` | 5 |
| `feedback/` | 5 |
| `shopping/` | 4 |
| `layout/` | 3 |
| `catalog/` | 3 |
| `ai/` | 3 |
| `notifications/` | 2 |
| Other | 4 |

### Hooks (12)

`useAiStylist`, `useAsyncAction`, `useAuth`, `useCart`, `useCommunity`, `useDebouncedValue`, `useInstantSearch`, `useInternational`, `useLoyalty`, `useMotionConfig`, `useOfflineSync`, `useWishlist`

### Contexts (8)

Auth, Cart, Wishlist, Community, Loyalty, International, AiStylist, Notification

## Backend

### Controllers (24)

| Domain | Count |
|--------|------:|
| Admin | 5 |
| Auth | 3 |
| Catalog | 3 |
| Shopping | 5 |
| Style | 1 |
| Community | 1 |
| Creator | 1 |
| Loyalty | 1 |
| Supplier | 1 |
| Notifications | 1 |
| Webhooks | 1 |
| Base | 1 |

### Services (27)

| Domain | Count |
|--------|------:|
| Enterprise | 8 |
| Shopping | 7 |
| Style | 3 |
| Catalog | 2 |
| Loyalty | 2 |
| Admin | 1 |
| Auth | 1 |
| Media | 1 |
| Search | 1 |
| Supplier | 1 |

### Repositories (6)

Cart, CreatorProfile, LoyaltyWallet, Product, User, Wishlist

### Models (101)

Covering all domain entities across catalog, shopping, AI, community, loyalty, supplier, admin, and enterprise infrastructure.

### Policies (8)

CommunityPost, CreatorCollection, EthicalPassport, Lookboard, ProductAlert, Product, SupplierProfile, User

### Middleware (7)

EnsureApiVersion, EnsureUserHasPermission, EnsureUserHasRole, LogApiPerformance, LogSlowQueries, SecurityHeaders, ValidateUpload

### Queue Jobs (7)

GenerateReportExport, ProcessCloudinaryUpload, ProcessIncomingWebhook, RefreshRecommendations, RunDatabaseBackup, SendTransactionalEmail, SyncSupplierInventory

### Artisan Commands (3)

ScanOrphanFiles, ValidateRelease, VeloraScheduler

## Testing

| Metric | Count |
|--------|------:|
| Test files | 23 |
| Test methods | 51 |
| Assertions | 349 |
| Feature tests | 22 |
| Unit tests | 1 |

## Build Artifacts

| Artifact | Size (gzip) |
|----------|------------|
| Main bundle | 55.6 KB |
| Vendor bundle | 71.9 KB |
| Motion bundle | 42.0 KB |
| Admin panel chunk | 9.2 KB |
| Supplier portal chunk | 2.4 KB |
| PWA precache | 883 KB (16 entries) |

## Documentation

| Category | Files |
|----------|------:|
| Root docs | 5 (README, CHANGELOG, RELEASE_NOTES, CONTRIBUTING, LICENSE) |
| Core guides | 15 |
| Release docs | 16 |
| Diagrams | 1 |
| Portfolio | 6 |
| Demo/presentation | 2 |
| **Total** | **45+** |
