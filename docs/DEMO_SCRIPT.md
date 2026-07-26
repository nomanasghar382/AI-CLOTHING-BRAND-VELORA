# VELORA Demonstration Script

**Duration:** 15–20 minutes  
**Version:** 1.0.0  
**Audience:** University evaluators, technical reviewers

## Pre-Demo Setup

```bash
# Terminal 1 — Backend
cd backend
VELORA_DEMO_MODE=true php artisan migrate:fresh --seed
php artisan serve

# Terminal 2 — Frontend
cd frontend
npm run dev
```

Open: `http://localhost:5173`

| Role | Email | Password |
|------|-------|----------|
| Customer | customer@velora.test | VeloraDemo!2026 |
| Creator | creator@velora.test | VeloraDemo!2026 |
| Supplier | supplier@velora.test | VeloraDemo!2026 |
| Admin | admin@velora.test | VeloraDemo!2026 |

---

## Segment 1: Project Introduction (2 min)

**Talking points:**

- VELORA is an AI-powered global modest fashion commerce platform
- Full-stack: React PWA + Laravel REST API + MySQL (144 tables)
- 24 feature modules, 198 API endpoints, 51 automated tests
- Tagline: "Style, Intelligently Yours"

**Show:** Homepage at `http://localhost:5173` — hero section, featured products, navigation.

---

## Segment 2: Technology Stack (1 min)

**Talking points:**

| Layer | Technology |
|-------|------------|
| Frontend | React 19, Vite, Bootstrap 5, PWA |
| Backend | Laravel 12, Sanctum, REST API |
| Database | MySQL 8 (144 tables) |
| AI | OpenAI (server-side only) |
| Media | Cloudinary (server-side only) |
| Payments | Stripe |

**Show:** Open browser DevTools → Network tab → demonstrate API calls to `/api/v1/products`.

---

## Segment 3: Authentication (1 min)

**Actions:**

1. Click "Sign In" → login as `customer@velora.test`
2. Show successful authentication and navbar update (cart icon, profile)

**Talking points:**

- Laravel Sanctum token-based authentication
- Role-based access control (customer, creator, supplier, admin)
- Device trust and security activity logging

---

## Segment 4: Customer Journey — Catalog & Search (2 min)

**Actions:**

1. Navigate to Catalog → browse products with filters
2. Use instant search bar → type "abaya" or "hijab"
3. Click a product → view detail page (images, sizes, colors, reviews)
4. Add to wishlist → show wishlist page

**Talking points:**

- Paginated catalog with dynamic filters
- Instant search with suggestions
- Product detail with variants, ethical passport badge
- Wishlist persistence across sessions

---

## Segment 5: AI Style Profile (1.5 min)

**Actions:**

1. Navigate to AI → Style Quiz
2. Complete a few quiz questions (body type, color preference, modesty level)
3. View Style Profile page with generated preferences

**Talking points:**

- Style quiz captures body type, color palette, modesty preferences
- Profile stored in `ai_profiles` table
- All AI processing server-side — OpenAI key never in React

---

## Segment 6: AI Stylist & Outfit Builder (2 min)

**Actions:**

1. Navigate to AI Stylist Chat → ask "What should I wear for a summer wedding?"
2. Show AI response with product recommendations
3. Navigate to Occasion Form → select "Eid celebration" → generate outfit
4. Save outfit to Saved Outfits

**Talking points:**

- Recommendations grounded in real catalog inventory
- Occasion-based styling with weather context
- Conversational AI stylist with chat history
- Response caching for performance

---

## Segment 7: Wardrobe & Visual Search (1.5 min)

**Actions:**

1. Navigate to Wardrobe → show digital closet items
2. Navigate to Visual Search → upload an image (or use demo)
3. Show similar product matches

**Talking points:**

- Digital wardrobe for outfit coordination
- Visual search using Cloudinary + similarity matching
- Wardrobe items can be mixed with catalog products

---

## Segment 8: Creator Marketplace (1 min)

**Actions:**

1. Logout → login as `creator@velora.test`
2. Navigate to Community → Marketplace
3. Show creator profile, collections, and lookboards

**Talking points:**

- Creator commerce with collections and commissions
- Lookboards for curated styling
- Earnings tracking and withdrawal system

---

## Segment 9: Community (1 min)

**Actions:**

1. Navigate to Community feed
2. Show posts with likes, comments, bookmarks
3. Create a quick post (optional)

**Talking points:**

- Social platform within the commerce ecosystem
- Moderation and reporting system
- Community activities and engagement tracking

---

## Segment 10: Shopping & Checkout (2 min)

**Actions:**

1. Logout → login as `customer@velora.test`
2. Add product to cart → view cart
3. Proceed to checkout → fill shipping details
4. Show order confirmation (demo mode uses test Stripe)

**Talking points:**

- Cart with quantity management and coupon support
- Checkout with address validation
- Stripe payment integration (test mode in demo)
- Order tracking and status history

---

## Segment 11: International Commerce (1 min)

**Actions:**

1. Navigate to International page
2. Change country/currency → show shipping estimate
3. View regional size guide

**Talking points:**

- Multi-currency support with live conversion rates
- Shipping zones, duty rules, and tax calculation
- Regional size guide for cross-border shopping

---

## Segment 12: Loyalty & Ethical Fashion Passport (1 min)

**Actions:**

1. Navigate to Loyalty → show points wallet and VIP tier
2. View referral code
3. Click ethical passport badge on a product → show sustainability evidence

**Talking points:**

- Points-based loyalty with VIP tiers
- Referral program and gift card redemption
- Ethical fashion passport with verified sustainability claims

---

## Segment 13: Supplier Portal (1 min)

**Actions:**

1. Logout → login as `supplier@velora.test`
2. Navigate to Supplier Portal
3. Show inventory levels, purchase orders, settlements

**Talking points:**

- Supplier self-service portal
- Inventory sync with webhook support
- Purchase order and settlement management

---

## Segment 14: Admin Dashboard & Analytics (2 min)

**Actions:**

1. Logout → login as `admin@velora.test`
2. Navigate to Admin Panel → show dashboard KPIs
3. Press `Ctrl+K` → demonstrate command palette global search
4. Navigate to Operations → show queue status, scheduler
5. Show BI analytics and trend forecasts

**Talking points:**

- Enterprise admin with 74 API endpoints
- Command palette (⌘K) for power users
- Operations center: queues, scheduler, backups, feature flags
- Business intelligence with analytics and AI trend forecasting

---

## Segment 15: PWA & Security (1 min)

**Actions:**

1. Show browser install prompt (PWA)
2. Open DevTools → Application → Service Worker
3. Briefly mention security headers, RBAC, audit logging

**Talking points:**

- Progressive Web App with offline support
- Service worker caches static assets
- Security: Sanctum, RBAC, CSP headers, webhook verification
- All secrets server-side only

---

## Segment 16: Deployment & Conclusion (1 min)

**Talking points:**

- Docker-ready with `docker-compose.yml`
- GitHub Actions CI/CD pipeline
- `php artisan velora:validate-release` for certification
- 51 automated tests, production build verified
- Documentation: 45+ guides covering all aspects

**Closing:**

> "VELORA demonstrates a production-ready full-stack platform combining commerce, AI, community, and enterprise operations — built with modern web technologies and security best practices."

---

## Q&A Preparation

| Question | Answer |
|----------|--------|
| Why modest fashion? | Underserved market with unique sizing, styling, and cultural needs |
| How is AI grounded? | Recommendations map to real catalog products via RecommendationService |
| How are secrets protected? | All API keys in Laravel `.env` only; React never receives credentials |
| Database size? | 144 tables across 21 migrations, normalized schema |
| Test coverage? | 51 PHPUnit tests (349 assertions), lint + build verified |
| Scalability? | Redis cache/queue, background jobs, code splitting, PWA |
| What would you add next? | Mobile app, real-time chat, advanced ML personalization |
