# VELORA System Diagrams

**Version:** 1.0.0

All diagrams use Mermaid syntax. Render in GitHub, VS Code, or any Mermaid-compatible viewer.

---

## Overall Architecture

```mermaid
graph TB
    subgraph Client["Client Layer"]
        PWA["React PWA<br/>Bootstrap 5 + Vite"]
        SW["Service Worker<br/>Offline Cache"]
    end

    subgraph API["API Layer"]
        Laravel["Laravel 12<br/>REST API v1/v2"]
        Sanctum["Sanctum Auth"]
        MW["Middleware Stack<br/>CORS · RBAC · Rate Limit"]
    end

    subgraph Data["Data Layer"]
        MySQL["MySQL 8<br/>144 Tables"]
        Redis["Redis<br/>Cache + Queue"]
    end

    subgraph External["External Services"]
        Stripe["Stripe<br/>Payments"]
        Cloudinary["Cloudinary<br/>Media"]
        OpenAI["OpenAI<br/>AI Stylist"]
    end

    PWA -->|HTTPS/JSON| Laravel
    SW -.->|Cache| PWA
    Laravel --> Sanctum
    Laravel --> MW
    MW --> MySQL
    MW --> Redis
    Laravel --> Stripe
    Laravel --> Cloudinary
    Laravel --> OpenAI
```

---

## Frontend Flow

```mermaid
flowchart LR
    subgraph Entry
        Index["index.html"]
        Router["React Router"]
    end

    subgraph Contexts["State Management"]
        Auth["AuthContext"]
        Cart["CartContext"]
        Wish["WishlistContext"]
        AI["AiStylistContext"]
        Loyalty["LoyaltyContext"]
        Intl["InternationalContext"]
    end

    subgraph Services
        API["apiClient<br/>Retry + Offline"]
        Admin["adminShoppingService"]
    end

    subgraph Pages
        Store["Storefront Pages<br/>31 pages"]
        AdminP["Admin Panel"]
        Supplier["Supplier Portal"]
    end

    Index --> Router
    Router --> Pages
    Pages --> Contexts
    Contexts --> Services
    Services -->|Bearer Token| API
    API -->|REST| Backend["Laravel API"]
```

---

## Backend Flow

```mermaid
flowchart TB
    Request["HTTP Request"] --> Route["Route Matching"]
    Route --> Version["EnsureApiVersion"]
    Version --> Auth["Sanctum Auth"]
    Auth --> Perm["EnsureUserHasPermission"]
    Perm --> Controller["Controller"]
    Controller --> Request["Form Request<br/>Validation"]
    Request --> Policy["Policy Check"]
    Policy --> Service["Service Layer"]
    Service --> Repo["Repository"]
    Repo --> Model["Eloquent Model"]
    Model --> DB["MySQL"]
    Service --> Job["Queue Job"]
    Service --> Resource["API Resource"]
    Resource --> Response["JSON Response"]
```

---

## Authentication Flow

```mermaid
sequenceDiagram
    participant U as User (React)
    participant F as Frontend
    participant A as Laravel API
    participant S as Sanctum
    participant D as Database

    U->>F: Enter credentials
    F->>A: POST /api/v1/auth/login
    A->>D: Validate user + password
    D-->>A: User record
    A->>S: Create personal access token
    S-->>A: Bearer token
    A-->>F: { token, user, permissions }
    F->>F: Store token (memory/localStorage)
    
    Note over F,A: Subsequent requests
    F->>A: GET /api/v1/auth/me<br/>Authorization: Bearer {token}
    A->>S: Validate token
    S-->>A: Authenticated user
    A->>D: Load roles + permissions
    A-->>F: User profile + RBAC

    U->>F: Logout
    F->>A: POST /api/v1/auth/logout
    A->>S: Revoke token
    A-->>F: 200 OK
```

---

## AI Recommendation Flow

```mermaid
sequenceDiagram
    participant U as Customer
    participant R as React (AI Pages)
    participant C as StyleController
    participant AI as AiFashionService
    participant Rec as RecommendationService
    participant Cat as ProductRepository
    participant O as OpenAI API
    participant DB as MySQL

    U->>R: Request outfit recommendation
    R->>C: POST /style/recommendations
    C->>DB: Load ai_profile + body_profile
    C->>Cat: Get available products (filtered)
    Cat-->>C: Product catalog subset
    C->>AI: Build prompt with profile + catalog
    AI->>O: Chat completion request
    O-->>AI: Structured outfit suggestions
    AI->>Rec: Map suggestions to products
    Rec->>DB: Save style_recommendations
    Rec->>DB: Cache in ai_response_caches
    Rec-->>C: RecommendationResource
    C-->>R: JSON response
    R-->>U: Display outfit with products
```

---

## Wardrobe Flow

```mermaid
flowchart TB
    subgraph Input
        Manual["Manual Add<br/>Photo + Details"]
        AI["AI Recommendation<br/>Save Outfit"]
        Purchase["Post-Purchase<br/>Add to Wardrobe"]
    end

    subgraph API
        WC["WardrobeController"]
        VS["VisualSearchService"]
    end

    subgraph Storage
        WI["wardrobe_items"]
        SA["saved_outfits"]
        FI["file_assets"]
    end

    subgraph Output
        Outfit["Outfit Builder<br/>Mix wardrobe + catalog"]
        Search["Visual Search<br/>Find similar items"]
    end

    Manual --> WC
    AI --> WC
    Purchase --> WC
    WC --> WI
    WC --> FI
    WI --> Outfit
    WI --> VS
    VS --> Search
    SA --> Outfit
```

---

## Creator Commerce Flow

```mermaid
flowchart LR
    subgraph Creator
        Profile["Creator Profile"]
        Collection["Collections"]
        Lookboard["Lookboards"]
    end

    subgraph Platform
        API["Creator API"]
        Commission["Commission Engine"]
        Withdrawal["Withdrawal System"]
    end

    subgraph Customer
        Browse["Browse Creators"]
        Shop["Shop Collection"]
        Follow["Follow Creator"]
    end

    Profile --> API
    Collection --> API
    Lookboard --> API
    Browse --> API
    Shop --> Commission
    Follow --> API
    Commission --> Withdrawal
```

---

## International Commerce Flow

```mermaid
flowchart TB
    subgraph Selection
        Country["Select Country"]
        Currency["Select Currency"]
        Address["International Address"]
    end

    subgraph Calculation
        Ship["Shipping Estimate<br/>Zones + Rates"]
        Duty["Duty Rules<br/>Country mappings"]
        Tax["Tax Rules<br/>Regional rates"]
        Size["Regional Size Guide"]
    end

    subgraph Checkout
        Convert["Currency Conversion<br/>currency_rates"]
        Total["Order Total<br/>Product + Ship + Duty + Tax"]
        Pay["Stripe Payment"]
    end

    Country --> Ship
    Country --> Duty
    Country --> Tax
    Currency --> Convert
    Address --> Ship
    Ship --> Total
    Duty --> Total
    Tax --> Total
    Convert --> Total
    Total --> Pay
    Country --> Size
```

---

## Database ER Diagram (Core Entities)

```mermaid
erDiagram
    users ||--o{ orders : places
    users ||--o| ai_profiles : has
    users ||--o| loyalty_wallets : has
    users ||--o| creator_profiles : has
    users ||--o| supplier_profiles : has
    users ||--o{ wardrobe_items : owns
    users ||--o{ community_posts : authors

    products ||--o{ product_variants : has
    products ||--o{ product_images : has
    products }o--|| categories : belongs_to
    products }o--|| brands : belongs_to
    products ||--o| ethical_passports : has

    orders ||--o{ order_items : contains
    orders ||--o| payments : has
    order_items }o--|| product_variants : references

    shopping_carts ||--o{ cart_items : contains
    cart_items }o--|| product_variants : references

    wishlists ||--o{ wishlist_items : contains
    wishlist_items }o--|| products : references

    creator_profiles ||--o{ creator_collections : creates
    creator_collections ||--o{ creator_collection_products : includes
    creator_collection_products }o--|| products : references

    community_posts ||--o{ community_comments : has
    community_posts ||--o{ community_likes : receives

    style_recommendations ||--o{ style_recommendation_items : contains
    style_recommendation_items }o--|| products : references

    users {
        bigint id PK
        string name
        string email
        string password
        timestamp email_verified_at
    }

    products {
        bigint id PK
        string name
        string slug
        decimal price
        bigint category_id FK
        bigint brand_id FK
    }

    orders {
        bigint id PK
        bigint user_id FK
        string status
        decimal total
        string currency
    }
```

---

## Admin Architecture

```mermaid
graph TB
    subgraph AdminUI["Admin Panel (React)"]
        Dashboard["Dashboard<br/>KPIs + Activity"]
        CommandPalette["Command Palette<br/>⌘K Search"]
        Topbar["Global Search"]
        Operations["Operations Center"]
        BI["BI Dashboard"]
    end

    subgraph AdminAPI["Admin API (74 endpoints)"]
        DashAPI["/admin/dashboard"]
        SearchAPI["/admin/search"]
        WorkspaceAPI["/admin/workspace"]
        CustomerAPI["/admin/customers"]
        ProductAPI["/admin/products"]
        OrderAPI["/admin/orders"]
        OpsAPI["/admin/operations"]
        HealthAPI["/admin/system/health"]
        BIAPI["/admin/bi"]
    end

    subgraph Security
        Sanctum["Sanctum Token"]
        RBAC["Role + Permission Check"]
        Audit["Audit Logging"]
    end

    AdminUI --> AdminAPI
    AdminAPI --> Sanctum
    Sanctum --> RBAC
    RBAC --> Audit
    Audit --> DB["MySQL"]
```

---

## Deployment Architecture

```mermaid
graph TB
    subgraph CDN["CDN / Static Host"]
        React["React Build<br/>frontend/dist"]
        SW["Service Worker"]
    end

    subgraph Server["Application Server"]
        Nginx["Nginx / Apache"]
        PHP["PHP-FPM<br/>Laravel"]
        Worker["Queue Worker"]
        Scheduler["Cron Scheduler"]
    end

    subgraph DataStore["Data Stores"]
        MySQL["MySQL 8"]
        Redis["Redis"]
    end

    subgraph External["External"]
        Stripe["Stripe"]
        Cloudinary["Cloudinary"]
        OpenAI["OpenAI"]
    end

    User["Users"] --> CDN
    User --> Nginx
    Nginx -->|/api/*| PHP
    Nginx -->|/*| React
    PHP --> MySQL
    PHP --> Redis
    Worker --> Redis
    Worker --> MySQL
    Scheduler --> PHP
    PHP --> External
```
