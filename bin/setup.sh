#!/usr/bin/env bash
set -euo pipefail

cd "$(dirname "$0")/.."

if [[ ! -f .env ]]; then cp .env.example .env; fi
set -a; source .env; set +a

WP() { docker compose run --rm wpcli wp "$@"; }

echo "→ Ensuring stack is up…"
docker compose up -d

echo "→ Waiting for WordPress (${WP_URL})…"
for i in $(seq 1 60); do
  if curl -sf -o /dev/null "$WP_URL"; then break; fi
  sleep 2
done

echo "→ Ensuring uploads dir is writable by www-data…"
docker compose exec -T wordpress chown -R www-data:www-data /var/www/html/wp-content/uploads || true

if ! WP core is-installed 2>/dev/null; then
  echo "→ Installing WordPress core…"
  WP core install \
    --url="$WP_URL" \
    --title="$WP_TITLE" \
    --admin_user="$WP_ADMIN_USER" \
    --admin_password="$WP_ADMIN_PASS" \
    --admin_email="$WP_ADMIN_EMAIL" \
    --skip-email
else
  echo "→ WordPress already installed."
fi

echo "→ Ensuring plugins are installed + active…"
for slug in advanced-custom-fields classic-editor; do
  if ! WP plugin is-active "$slug" 2>/dev/null; then
    WP plugin install "$slug" --activate
  fi
done

echo "→ Activating theme…"
WP theme activate funnie

echo "→ Running seed…"
WP eval-file bin/seed.php

echo ""
echo "✓ Ready!"
echo "  Site:       $WP_URL"
echo "  Admin:      $WP_URL/wp-admin  ($WP_ADMIN_USER / $WP_ADMIN_PASS)"
echo "  phpMyAdmin: http://localhost:8081"
echo "  Mailpit:    http://localhost:8025"
