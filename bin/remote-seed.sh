#!/usr/bin/env bash
# Runs on the VPS. Idempotent: safe to re-run after every deploy.
# Driven by env: SITE_DIR, SITE_URL, FUNNIE_DB_PASSWORD, WP_ADMIN_USER, WP_ADMIN_PASS, WP_ADMIN_EMAIL.

set -euo pipefail

: "${SITE_DIR:?missing SITE_DIR}"
: "${SITE_URL:?missing SITE_URL}"
: "${FUNNIE_DB_PASSWORD:?missing FUNNIE_DB_PASSWORD}"
: "${WP_ADMIN_USER:?missing WP_ADMIN_USER}"
: "${WP_ADMIN_PASS:?missing WP_ADMIN_PASS}"
: "${WP_ADMIN_EMAIL:?missing WP_ADMIN_EMAIL}"

# One-off wp-cli container — the wordpress:apache image doesn't ship wp-cli,
# so we run wordpress:cli-php8.3 on demand, mounting the same app dir.
WP() {
  docker run --rm \
    --network quailman-network \
    -v "$SITE_DIR/app:/var/www/html" \
    -v "$SITE_DIR/bin:/var/www/html/bin:ro" \
    --user 33:33 \
    -e WORDPRESS_DB_HOST=db-mysql \
    -e WORDPRESS_DB_USER=root \
    -e WORDPRESS_DB_PASSWORD="$FUNNIE_DB_PASSWORD" \
    -e WORDPRESS_DB_NAME=funnie \
    wordpress:cli-php8.3 wp "$@"
}

echo "→ Waiting for WordPress filesystem to be populated…"
for i in $(seq 1 30); do
  if [[ -f "$SITE_DIR/app/wp-includes/version.php" ]]; then break; fi
  sleep 2
done
if [[ ! -f "$SITE_DIR/app/wp-includes/version.php" ]]; then
  echo "✗ WordPress core not found in $SITE_DIR/app after 60s." >&2
  exit 1
fi

if ! WP core is-installed 2>/dev/null; then
  echo "→ Installing WordPress core…"
  WP core install \
    --url="$SITE_URL" \
    --title="Funnie — Bruno Oliveira" \
    --admin_user="$WP_ADMIN_USER" \
    --admin_password="$WP_ADMIN_PASS" \
    --admin_email="$WP_ADMIN_EMAIL" \
    --skip-email
else
  # Keep the public URL in sync with what the deploy script thinks it is.
  current_url=$(WP option get siteurl 2>/dev/null || echo '')
  if [[ "$current_url" != "$SITE_URL" ]]; then
    echo "→ Updating siteurl and home to $SITE_URL…"
    WP option update home     "$SITE_URL"
    WP option update siteurl  "$SITE_URL"
  fi
fi

echo "→ Ensuring plugins installed + active…"
for slug in advanced-custom-fields classic-editor; do
  if ! WP plugin is-active "$slug" 2>/dev/null; then
    WP plugin install "$slug" --activate
  fi
done

echo "→ Activating theme…"
WP theme activate funnie

echo "→ Running seed.php…"
WP eval-file bin/seed.php

echo "✓ Seed complete."
