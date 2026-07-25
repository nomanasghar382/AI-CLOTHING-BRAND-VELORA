# Production Checklist

- [ ] `APP_ENV=production` and `APP_DEBUG=false`
- [ ] `APP_KEY` generated and stored securely
- [ ] `VELORA_DEMO_MODE=false`
- [ ] Database migrated and seeded (without demo seeder in production)
- [ ] Queue worker running (`php artisan queue:work`)
- [ ] Scheduler running (`php artisan velora:scheduler-run` via cron)
- [ ] Redis or database cache configured
- [ ] Stripe, Cloudinary, OpenAI keys configured in `.env`
- [ ] `FRONTEND_URL` and `APP_URL` set correctly
- [ ] HTTPS enabled with HSTS
- [ ] Backups scheduled and verified
- [ ] `php artisan velora:validate-release` passes
- [ ] Full test suite passes
- [ ] Frontend production build deployed
