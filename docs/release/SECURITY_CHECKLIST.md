# Security Checklist

- [ ] All secrets in Laravel `.env` only (never in React)
- [ ] Sanctum token authentication enforced on protected routes
- [ ] Role and permission middleware on admin endpoints
- [ ] Security headers middleware enabled
- [ ] Upload validation middleware active
- [ ] Rate limiting on auth, API, uploads, and webhooks
- [ ] Password history and concurrent session limits configured
- [ ] Audit logging enabled for security events
- [ ] Webhook signature verification configured
- [ ] CSP and HSTS configured for production
- [ ] CORS restricted to `FRONTEND_URL`
