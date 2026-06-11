#!/usr/bin/env bash
# Deploys the funnie theme + seed data to the quailman VPS via dockhand API.
# Idempotent. Re-run after every change.
#
# Reads config from .env.deploy (gitignored). See .env.deploy.example for the schema.

set -euo pipefail

cd "$(dirname "$0")/.."

ENV_FILE=".env.deploy"
if [[ ! -f "$ENV_FILE" ]]; then
  echo "✗ $ENV_FILE not found. Copy .env.deploy.example and fill in the values." >&2
  exit 1
fi
set -a; source "$ENV_FILE"; set +a

: "${DOCKHAND_TOKEN:?missing DOCKHAND_TOKEN}"
: "${FUNNIE_DB_PASSWORD:?missing FUNNIE_DB_PASSWORD}"
: "${WP_ADMIN_USER:?missing WP_ADMIN_USER}"
: "${WP_ADMIN_PASS:?missing WP_ADMIN_PASS}"
: "${WP_ADMIN_EMAIL:?missing WP_ADMIN_EMAIL}"

# Defaults (override in .env.deploy).
VPS_HOST="${VPS_HOST:-root@quailman.lan}"
VPS_PORT="${VPS_PORT:-8088}"
VPS_SITE_DIR="${VPS_SITE_DIR:-/root/apps/sites/funnie.dev}"
SITE_URL="${SITE_URL:-https://funnie.dev}"
DOCKHAND_URL="${DOCKHAND_URL:-http://localhost:3000}"
DOCKHAND_ENV="${DOCKHAND_ENV:-1}"
STACK_NAME="${STACK_NAME:-funniedev-website}"
COMPOSE_FILE="${COMPOSE_FILE:-deploy/funnie-stack.yml}"

SSH_ARGS=(-p "$VPS_PORT" -o BatchMode=yes)
ssh_exec() { ssh "${SSH_ARGS[@]}" "$VPS_HOST" "$@"; }
rsync_opts=(-az --delete -e "ssh -p $VPS_PORT")

# Shorthand for dockhand calls executed on the VPS.
# Usage: dh_api METHOD /path [json_body]
dh_api() {
  local method="$1" path="$2" body="${3:-}"
  local cmd="curl -sS -X $method -H 'Authorization: Bearer $DOCKHAND_TOKEN' -H 'Content-Type: application/json' '$DOCKHAND_URL$path?env=$DOCKHAND_ENV'"
  if [[ -n "$body" ]]; then
    # body arrives on stdin via ssh
    printf '%s' "$body" | ssh_exec "$cmd --data-binary @-"
  else
    ssh_exec "$cmd"
  fi
}

echo "== Preflight =="
ssh_exec "echo connected" >/dev/null
dh_api GET /api/environments >/dev/null

if [[ ! -f "$COMPOSE_FILE" ]]; then
  echo "✗ $COMPOSE_FILE not found" >&2
  exit 1
fi

echo "== Preparing remote dirs =="
ssh_exec "mkdir -p '$VPS_SITE_DIR/app/wp-content/themes' '$VPS_SITE_DIR/app/wp-content/mu-plugins' '$VPS_SITE_DIR/bin'"

echo "== Rsync theme =="
rsync "${rsync_opts[@]}" wp-content/themes/funnie/ "$VPS_HOST:$VPS_SITE_DIR/app/wp-content/themes/funnie/"

echo "== Rsync mu-plugins =="
rsync "${rsync_opts[@]}" wp-content/mu-plugins/ "$VPS_HOST:$VPS_SITE_DIR/app/wp-content/mu-plugins/"

echo "== Rsync bin/ =="
rsync "${rsync_opts[@]}" bin/ "$VPS_HOST:$VPS_SITE_DIR/bin/"

echo "== Fixing ownership (UID:GID 33:33 = www-data in WP image) =="
ssh_exec "chown -R 33:33 '$VPS_SITE_DIR/app/wp-content' '$VPS_SITE_DIR/bin'"

echo "== Stack: create or update =="
STACK_EXISTS="$(dh_api GET /api/stacks | python3 -c "import json,sys; n='$STACK_NAME'; print('yes' if any(s.get('name')==n for s in json.load(sys.stdin)) else 'no')")"

build_env_vars_json() {
  # Dockhand only reliably interpolates envVars into compose when they are
  # flagged isSecret (non-secret vars don't make it into the stack's .env
  # file). Mark everything secret; dockhand redacts values in the UI.
  FUNNIE_DB_PASSWORD="$FUNNIE_DB_PASSWORD" \
  python3 - <<'PY'
import json, os
vars = [
    {"key": "FUNNIE_DB_PASSWORD", "value": os.environ["FUNNIE_DB_PASSWORD"], "isSecret": True},
]
print(json.dumps(vars))
PY
}

if [[ "$STACK_EXISTS" == "no" ]]; then
  echo "→ Creating stack '$STACK_NAME' (start=true)…"
  env_vars_json="$(build_env_vars_json)"
  body="$(STACK_NAME="$STACK_NAME" COMPOSE_FILE="$COMPOSE_FILE" ENV_VARS_JSON="$env_vars_json" python3 - <<'PY'
import json, os
with open(os.environ["COMPOSE_FILE"]) as f:
    compose = f.read()
body = {
    "name": os.environ["STACK_NAME"],
    "compose": compose,
    "envVars": json.loads(os.environ["ENV_VARS_JSON"]),
    "start": True,
}
print(json.dumps(body))
PY
)"
  resp="$(dh_api POST /api/stacks "$body")"
  echo "$resp"
else
  echo "→ Updating compose for existing stack '$STACK_NAME'…"
  body="$(COMPOSE_FILE="$COMPOSE_FILE" python3 - <<'PY'
import json, os
with open(os.environ["COMPOSE_FILE"]) as f:
    compose = f.read()
print(json.dumps({"content": compose}))
PY
)"
  dh_api PUT /api/stacks/"$STACK_NAME"/compose "$body" >/dev/null

  echo "→ Syncing env vars…"
  env_vars_json="$(build_env_vars_json)"
  env_body="$(ENV_VARS_JSON="$env_vars_json" python3 -c 'import json,os; print(json.dumps({"variables": json.loads(os.environ["ENV_VARS_JSON"])}))')"
  dh_api PUT /api/stacks/"$STACK_NAME"/env "$env_body" >/dev/null
  echo "→ Restarting stack (down + start to pick up new compose)…"
  dh_api POST /api/stacks/"$STACK_NAME"/down  '{}' >/dev/null || true
  # dockhand jobs are async — wait for the stack to drop out of "running"
  # before asking it to come back up, otherwise the start is a no-op.
  for i in $(seq 1 30); do
    status="$(dh_api GET /api/stacks | python3 -c "import json,sys; n=sys.argv[1]; print(next((s.get('status','') for s in json.load(sys.stdin) if s.get('name')==n), ''))" "$STACK_NAME")"
    [[ "$status" != "running" ]] && break
    sleep 2
  done
  dh_api POST /api/stacks/"$STACK_NAME"/start '{}' >/dev/null
fi

echo "== Waiting for funniedev-wordpress to respond (up to ~2 min for a cold first deploy) =="
ok=0
for i in $(seq 1 60); do
  if ssh_exec "docker exec funniedev-wordpress curl -sf -o /dev/null http://localhost/wp-login.php" 2>/dev/null; then
    ok=1; break
  fi
  sleep 2
done
if [[ "$ok" -ne 1 ]]; then
  echo "✗ funniedev-wordpress not responding after 120s. Check 'docker logs funniedev-wordpress' on the VPS." >&2
  exit 1
fi

echo "== Remote seed =="
ssh_exec "SITE_DIR='$VPS_SITE_DIR' \
          SITE_URL='$SITE_URL' \
          FUNNIE_DB_PASSWORD='$FUNNIE_DB_PASSWORD' \
          WP_ADMIN_USER='$WP_ADMIN_USER' \
          WP_ADMIN_PASS='$WP_ADMIN_PASS' \
          WP_ADMIN_EMAIL='$WP_ADMIN_EMAIL' \
          bash '$VPS_SITE_DIR/bin/remote-seed.sh'"

echo ""
echo "✓ Deploy complete."
echo "  Site:     $SITE_URL"
echo "  Admin:    $SITE_URL/wp-admin ($WP_ADMIN_USER)"
echo ""
echo "  NPM reminder: proxy host for funnie.dev must point to funniedev-wordpress:80 (scheme http)."
