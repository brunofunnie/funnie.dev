#!/usr/bin/env bash
# Lightweight theme-only deploy: rsyncs wp-content/themes/funnie/ to the VPS.
# Skips the dockhand stack work, mu-plugins, bin/, plugin install, and the
# seed reset. Use this for fast iteration on assets/PHP after the full
# deploy.sh has stood the stack up at least once.
#
# Reads the same .env.deploy file as deploy.sh.

set -euo pipefail

cd "$(dirname "$0")/.."

ENV_FILE=".env.deploy"
if [[ ! -f "$ENV_FILE" ]]; then
  echo "✗ $ENV_FILE not found. Copy .env.deploy.example and fill in the values." >&2
  exit 1
fi
set -a; source "$ENV_FILE"; set +a

VPS_HOST="${VPS_HOST:-root@quailman.lan}"
VPS_PORT="${VPS_PORT:-8088}"
VPS_SITE_DIR="${VPS_SITE_DIR:-/root/apps/sites/funnie.dev}"
SITE_URL="${SITE_URL:-https://funnie.dev}"
THEME_SLUG="${THEME_SLUG:-funnie}"

SSH_ARGS=(-p "$VPS_PORT" -o BatchMode=yes)
ssh_exec() { ssh "${SSH_ARGS[@]}" "$VPS_HOST" "$@"; }
rsync_opts=(-az --delete -e "ssh -p $VPS_PORT")

LOCAL_THEME="wp-content/themes/$THEME_SLUG/"
REMOTE_THEME="$VPS_SITE_DIR/app/wp-content/themes/$THEME_SLUG/"

if [[ ! -d "$LOCAL_THEME" ]]; then
  echo "✗ $LOCAL_THEME not found locally — nothing to push." >&2
  exit 1
fi

echo "== Preflight =="
ssh_exec "echo connected" >/dev/null
ssh_exec "mkdir -p '$REMOTE_THEME'"

echo "== Rsync theme ($THEME_SLUG) =="
rsync "${rsync_opts[@]}" "$LOCAL_THEME" "$VPS_HOST:$REMOTE_THEME"

echo "== Fixing ownership (UID:GID 33:33 = www-data in WP image) =="
ssh_exec "chown -R 33:33 '$REMOTE_THEME'"

echo ""
echo "✓ Theme deploy complete."
echo "  Site:  $SITE_URL"
echo "  Path:  $REMOTE_THEME"
