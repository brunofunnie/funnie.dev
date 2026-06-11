#!/usr/bin/env bash
set -euo pipefail

BASE="${WP_URL:-http://localhost:8080}"
FAIL=0

check_200() {
  local url="$1"
  local code
  code=$(curl -s -o /dev/null -w '%{http_code}' "$url")
  if [[ "$code" == "200" ]]; then
    echo "  ✓ 200 $url"
  else
    echo "  ✗ $code $url"
    FAIL=1
  fi
}

check_contains() {
  local url="$1"; local needle="$2"
  local hits
  hits=$(curl -sL "$url" | grep -F -c -- "$needle" || true)
  if [[ "$hits" -gt 0 ]]; then
    echo "  ✓ contains '$needle' at $url"
  else
    echo "  ✗ missing '$needle' at $url"
    FAIL=1
  fi
}

echo "== Smoke tests =="
check_200 "$BASE/"
check_200 "$BASE/wp-login.php"

for id in hero panel-about panel-hardware panel-blog-day panel-blog-night panel-socials; do
  check_contains "$BASE/" "id=\"$id\""
done

if [[ $FAIL -eq 0 ]]; then
  echo "✓ All smoke checks passed."
else
  echo "✗ Smoke checks failed."
  exit 1
fi
