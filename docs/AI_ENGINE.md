# VELORA AI Fashion Engine

**Version:** 1.0.0

## Overview

The AI Fashion Engine provides personalized styling grounded in real catalog inventory. All AI processing occurs server-side; API keys never reach the React frontend.

## Architecture

```
User Request → StyleController → AiFashionService → OpenAI API
                      ↓                    ↓
              RecommendationService   ProductRepository
                      ↓                    ↓
              Catalog Products      ai_response_caches
```

See [diagrams/ARCHITECTURE_DIAGRAMS.md](diagrams/ARCHITECTURE_DIAGRAMS.md#ai-recommendation-flow) for the full flow diagram.

## Components

| Component | Path | Responsibility |
|-----------|------|----------------|
| StyleController | `Http/Controllers/Api/V1/Style/StyleController.php` | API endpoints |
| AiFashionService | `Services/Style/AiFashionService.php` | OpenAI integration |
| RecommendationService | `Services/Style/RecommendationService.php` | Catalog-grounded matching |
| WeatherService | `Services/Style/WeatherService.php` | Occasion weather context |
| VisualSearchService | `Services/Search/VisualSearchService.php` | Image similarity search |
| RefreshRecommendationsJob | `Jobs/RefreshRecommendationsJob.php` | Background refresh |

## Features

### 1. Style Profile

Users complete an interactive style quiz that captures:

- Body type and fit preferences
- Color palette affinity
- Modesty level and coverage preferences
- Occasion frequency (work, casual, formal, events)
- Budget range

**Storage:** `ai_profiles`, `body_profiles`  
**Endpoint:** `POST /api/v1/style/quiz`

### 2. AI Recommendations

Generates outfit suggestions from catalog products filtered by:

- User style profile
- Season and occasion
- Product availability and inventory
- Price range

**Storage:** `style_recommendations`, `style_recommendation_items`  
**Endpoint:** `POST /api/v1/style/recommendations`  
**Caching:** `ai_response_caches` (reduces duplicate API calls)

### 3. Occasion Outfit Builder

Creates complete looks for specific events:

- Wedding, Eid, work meeting, casual outing, etc.
- Weather-aware when `WEATHER_API_KEY` is configured
- Respects modesty and coverage preferences

**Endpoint:** `POST /api/v1/style/outfits/occasion`

### 4. AI Stylist Chat

Conversational styling assistant powered by OpenAI:

- Context-aware responses using style profile
- Product suggestions linked to catalog
- Conversation history preserved

**Storage:** `style_conversations`, `style_messages`  
**Endpoint:** `POST /api/v1/style/chat`

### 5. Saved Outfits

Users save AI-generated or manually curated outfits:

**Storage:** `saved_outfits`  
**Endpoints:** `GET/POST /api/v1/style/saved-outfits`

### 6. Visual Search

Image-based product discovery:

- Upload via Cloudinary (server-side)
- Similarity matching against catalog images
- Results cached in `visual_similarity_caches`

**Endpoint:** `POST /api/v1/visual-searches`

### 7. Fashion Trends

Editorial trend data for inspiration:

**Storage:** `fashion_trends`  
**Endpoint:** `GET /api/v1/style/trends`

### 8. Feedback Loop

Users rate recommendations to improve future suggestions:

**Storage:** `style_feedback`  
**Endpoint:** `POST /api/v1/style/feedback`

## Configuration

All credentials in `backend/.env` only:

```env
OPENAI_API_KEY=sk-...
OPENAI_API_URL=https://api.openai.com/v1
OPENAI_MODEL=gpt-4o-mini
OPENAI_TIMEOUT=15

# Optional weather context
WEATHER_API_KEY=...
WEATHER_API_URL=https://api.openweathermap.org/data/2.5
WEATHER_TIMEOUT=8
```

## Data Flow

1. User submits style quiz or recommendation request
2. `StyleController` validates input via Form Requests
3. `AiFashionService` builds prompt with profile + catalog context
4. OpenAI returns structured outfit suggestions
5. `RecommendationService` maps suggestions to real products
6. Response cached; results returned via API Resources
7. Background job refreshes stale recommendations on schedule

## Error Handling

| Condition | Response |
|-----------|----------|
| Missing OpenAI key | 503 with clear message |
| OpenAI timeout | 504 gateway timeout |
| No matching products | 200 with empty suggestions + message |
| Invalid image upload | 422 validation error |
| Cloudinary unavailable | 503 service unavailable |

## Performance

- Response caching in `ai_response_caches` (TTL configurable)
- `RefreshRecommendationsJob` runs on scheduler
- Slow request logging at `VELORA_SLOW_REQUEST_MS` threshold
- Eager loading on recommendation queries (no N+1)

## Security

- OpenAI API key never exposed to frontend
- User can only access own profile, conversations, and saved outfits
- Style endpoints require Sanctum authentication
- Upload validation middleware on image endpoints

## React Integration

| Page | Hook/Context | API |
|------|-------------|-----|
| StyleQuizPage | AiStylistContext | `/style/quiz` |
| StyleProfilePage | AiStylistContext | `/style/profile` |
| StylistChatPage | useAiStylist | `/style/chat` |
| SavedOutfitsPage | useAiStylist | `/style/saved-outfits` |
| OccasionFormPage | useAiStylist | `/style/outfits/occasion` |
| VisualSearchPage | — | `/visual-searches` |
| WardrobePage | — | `/wardrobe` |

## Related Documentation

- [release/AI_SERVICES.md](release/AI_SERVICES.md) — Detailed service documentation
- [USER_GUIDE.md](USER_GUIDE.md) — Customer-facing AI features
- [API_REFERENCE.md](API_REFERENCE.md) — Endpoint reference
