# AI Services Documentation

## Overview

VELORA's AI Fashion Engine provides style profiling, outfit recommendations, occasion-based styling, conversational assistance, and trend insights.

## Configuration

All AI credentials are server-side only:

```env
OPENAI_API_KEY=sk-...
OPENAI_API_URL=https://api.openai.com/v1
OPENAI_MODEL=gpt-4o-mini
OPENAI_TIMEOUT=15
```

Optional weather context:

```env
WEATHER_API_KEY=...
WEATHER_API_URL=https://api.openweathermap.org/data/2.5
```

## Services

### Style Profile

- Users complete a style quiz
- Profile stored in `ai_profiles` and `body_profiles`
- Preferences inform all recommendation requests

### Recommendations

- `POST /api/v1/style/recommendations` generates outfit suggestions
- Uses catalog products filtered by profile, season, and availability
- Results cached in `ai_response_caches` for performance

### Occasion Outfits

- `POST /api/v1/style/outfits/occasion` generates occasion-specific looks
- Considers weather when `WEATHER_API_KEY` is configured

### Style Chat

- `POST /api/v1/style/chat` provides conversational styling assistance
- Conversations stored in `style_conversations` and `style_messages`
- Requires valid OpenAI API key

### Trends

- Editorial trends seeded in `fashion_trends`
- `GET /api/v1/style/trends` returns active trend data
- BI analytics tracks recommendation click-through

### Visual Search

- `POST /api/v1/visual-searches` accepts uploaded images
- Uses Cloudinary when configured, falls back to catalog similarity
- Results cached in `visual_similarity_caches`

### Feedback Loop

- Users submit feedback via `POST /api/v1/style/feedback`
- Stored in `style_feedback` for recommendation improvement

## Error Handling

- Missing OpenAI key returns graceful API errors (no placeholder responses)
- Cloudinary upload failures return 503 with clear message
- AI responses respect timeout configuration

## Performance

- Response caching reduces duplicate AI calls
- Recommendations job (`RefreshRecommendationsJob`) runs on schedule
- Slow request logging at `VELORA_SLOW_REQUEST_MS` threshold
