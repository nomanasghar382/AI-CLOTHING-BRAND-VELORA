#!/usr/bin/env bash
# VELORA v1.0.0 Submission Verification Script
# Usage: bash scripts/verify-submission.sh

set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
PASS=0
FAIL=0
WARN=0

green()  { printf '\033[0;32m✓\033[0m %s\n' "$1"; PASS=$((PASS + 1)); }
red()    { printf '\033[0;31m✗\033[0m %s\n' "$1"; FAIL=$((FAIL + 1)); }
yellow() { printf '\033[0;33m!\033[0m %s\n' "$1"; WARN=$((WARN + 1)); }

echo "============================================"
echo "  VELORA v1.0.0 Submission Verification"
echo "============================================"
echo ""

# --- Version ---
echo "--- Version ---"
VERSION=$(cat "$ROOT/VERSION" 2>/dev/null || echo "MISSING")
if [ "$VERSION" = "1.0.0" ]; then
  green "VERSION is 1.0.0"
else
  red "VERSION is '$VERSION' (expected 1.0.0)"
fi

# --- Required files ---
echo ""
echo "--- Required Files ---"
REQUIRED_FILES=(
  "README.md"
  "LICENSE"
  "CHANGELOG.md"
  "RELEASE_NOTES.md"
  "CONTRIBUTING.md"
  "VERSION"
  "docs/PROJECT_OVERVIEW.md"
  "docs/ARCHITECTURE.md"
  "docs/API_REFERENCE.md"
  "docs/DATABASE.md"
  "docs/AI_ENGINE.md"
  "docs/INSTALLATION.md"
  "docs/DEPLOYMENT.md"
  "docs/SECURITY.md"
  "docs/FEATURE_MATRIX.md"
  "docs/PROJECT_STATISTICS.md"
  "docs/DEMO_SCRIPT.md"
  "docs/PRESENTATION.md"
  "docs/RELEASE_SUMMARY.md"
  "docs/VERIFICATION.md"
  "docs/diagrams/ARCHITECTURE_DIAGRAMS.md"
  "docs/portfolio/PORTFOLIO.md"
  "docs/portfolio/CASE_STUDY.md"
  "backend/.env.example"
)

for f in "${REQUIRED_FILES[@]}"; do
  if [ -f "$ROOT/$f" ]; then
    green "$f exists"
  else
    red "$f MISSING"
  fi
done

# --- Backend tests ---
echo ""
echo "--- Backend Tests ---"
TEST_OUTPUT=$(cd "$ROOT/backend" && php artisan test --no-ansi 2>&1)
if echo "$TEST_OUTPUT" | grep -q "Tests:.*51 passed"; then
  green "PHPUnit: 51 passed (349 assertions)"
else
  red "PHPUnit tests failed"
  echo "$TEST_OUTPUT" | tail -5
fi

# --- Release validation ---
echo ""
echo "--- Release Validation ---"
if cd "$ROOT/backend" && php artisan velora:validate-release --migrate --no-ansi 2>/dev/null | grep -q "Release validation passed"; then
  green "velora:validate-release passed"
else
  red "velora:validate-release failed"
fi

# --- Frontend lint ---
echo ""
echo "--- Frontend Lint ---"
if cd "$ROOT/frontend" && npm run lint 2>/dev/null; then
  green "Frontend lint passed"
else
  red "Frontend lint failed"
fi

# --- Frontend build ---
echo ""
echo "--- Frontend Build ---"
if cd "$ROOT/frontend" && npm run build 2>/dev/null; then
  if [ -f "$ROOT/frontend/dist/index.html" ]; then
    green "Production build succeeded (dist/index.html present)"
  else
    red "Build ran but dist/index.html missing"
  fi
else
  red "Production build failed"
fi

# --- Git tag ---
echo ""
echo "--- Git Tag ---"
if git -C "$ROOT" tag -l "v1.0.0" | grep -q "v1.0.0"; then
  green "Git tag v1.0.0 exists"
else
  yellow "Git tag v1.0.0 not found locally"
fi

# --- Documentation count ---
echo ""
echo "--- Documentation ---"
DOC_COUNT=$(find "$ROOT/docs" -name "*.md" | wc -l)
if [ "$DOC_COUNT" -ge 25 ]; then
  green "$DOC_COUNT documentation files in docs/"
else
  yellow "Only $DOC_COUNT documentation files (expected 25+)"
fi

# --- Summary ---
echo ""
echo "============================================"
echo "  Results: $PASS passed, $FAIL failed, $WARN warnings"
echo "============================================"

if [ "$FAIL" -gt 0 ]; then
  echo "  STATUS: FAILED — fix issues above"
  exit 1
else
  echo "  STATUS: READY FOR SUBMISSION"
  exit 0
fi
