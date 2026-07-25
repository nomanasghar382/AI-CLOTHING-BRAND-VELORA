# Performance Checklist

- [ ] Frontend production build with code splitting (`npm run build`)
- [ ] PWA service worker caching enabled
- [ ] Image lazy loading on catalog and product pages
- [ ] API response caching via `CacheManagerService`
- [ ] Database indexes verified on foreign keys and status columns
- [ ] Slow query logging configured (`VELORA_SLOW_QUERY_MS`)
- [ ] Queue offloading for email, webhooks, backups, and reports
- [ ] Redis recommended for cache and queues at scale
- [ ] CDN configured for static assets and product images
- [ ] Lighthouse performance audit run on key pages
