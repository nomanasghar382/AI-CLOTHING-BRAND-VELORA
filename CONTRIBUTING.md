# Contributing to VELORA

**Version:** 1.0.0  
**Status:** Release freeze — documentation and defect fixes only.

## Release Freeze Policy

VELORA v1.0.0 is feature-complete. Contributions during the release freeze are limited to:

- Bug fixes (defects only, no behavior changes)
- Documentation improvements
- Test coverage for existing behavior
- Security patches
- Performance optimizations (no API changes)

**Not accepted during freeze:**

- New business features or modules
- Database schema changes
- API contract changes
- UI redesigns

## Development Setup

See [INSTALLATION.md](INSTALLATION.md) for full setup instructions.

```bash
# Backend
cd backend && composer install && cp .env.example .env
php artisan key:generate && php artisan migrate --seed

# Frontend
cd frontend && npm install && npm run dev
```

## Code Standards

### Backend (Laravel)

- PSR-12 coding style
- Service layer for business logic (controllers stay thin)
- Form Requests for validation
- API Resources for response shaping
- Policies for authorization
- Eloquent with eager loading (avoid N+1)
- Secrets in `.env` only — never in code or frontend

### Frontend (React)

- Functional components with hooks
- Bootstrap 5 + VELORA design system (Atelier theme)
- API calls via `apiClient` service
- Error handling via `getApiErrorMessage` utility
- Reduced motion support via `useMotionConfig`
- No secrets in React bundle

## Branch Naming

```
cursor/<descriptive-name>-48d0
```

## Commit Messages

```
type: concise description

feat: new capability (post-freeze only)
fix: defect correction
docs: documentation update
test: test addition or fix
refactor: code restructuring (no behavior change)
perf: performance improvement
```

## Testing Requirements

Before submitting changes:

```bash
cd backend && php artisan test                    # 51 tests must pass
cd backend && php artisan velora:validate-release --migrate
cd frontend && npm run lint && npm run build
```

## Pull Request Checklist

- [ ] No new business features (release freeze)
- [ ] No database schema changes
- [ ] No API contract changes
- [ ] Tests pass (51/51)
- [ ] Lint clean
- [ ] Build succeeds
- [ ] Documentation updated if behavior changed
- [ ] Secrets remain server-side only

## Reporting Issues

Use GitHub Issues with:

1. **Description** — What happened vs. expected
2. **Steps to reproduce**
3. **Environment** — OS, PHP/Node versions
4. **Screenshots** — If UI-related
5. **Logs** — Relevant backend log entries

## Documentation

When adding or changing documented behavior:

- Update relevant guide in `docs/`
- Update `CHANGELOG.md` under `[Unreleased]` or next version
- Update `API_REFERENCE.md` if endpoints change (post-freeze)

## License

By contributing, you agree that your contributions will be licensed under the project license. See [LICENSE](../LICENSE).
