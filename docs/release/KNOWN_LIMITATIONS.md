# Known Limitations

- Visual search uses catalog fallback when Cloudinary is not configured
- AI style chat requires a valid `OPENAI_API_KEY` in Laravel `.env`
- Stripe payment capture requires `STRIPE_SECRET` and webhook configuration
- Some admin sidebar pages (reviews, creators list) depend on future dedicated admin list endpoints
- Forecasting in admin uses transparent rolling baseline analytics, not a separate ML model
- International carrier tracking UI is ready but requires live carrier API integration
