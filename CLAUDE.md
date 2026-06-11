# funnie.dev — project notes

WordPress 6.9.4 site at `wp-content/themes/funnie/`. Custom theme, single page (front-page) + single posts. Tailwind via CDN runtime (config inlined in `inc/enqueue.php` `wp_head` action) + hand-written `assets/css/main.css`. No build step.

## Day / night metaphor

The site has two parallel "sides". Front page splits hero (left=day, right=night, mouse-reactive). Every other page is locked to one side based on the post's category (`professional` → day, anything else → night).

- `body[data-time="day"|"night"]` is the single source of truth for which side is active.
- On the front page, JS toggles it as the user mouses over the hero halves and as time of day changes.
- On single posts, an inline `<script>` at the top of `single.php` sets it from the post's category before main.js loads.
- `body.no-hero` is added by a `body_class` filter for non-front-page templates. It triggers the stripped-down JS init path and the sticky-footer flex layout.

## CSS variable system (sky/hill palette)

`applySkyAndHills(hour)` in `main.js` writes `--sky-1..5` and `--hill-far/mid/near/pine` to `:root`. **Anything that wants to track the live sky should reference these vars** — don't hardcode colors.

Time → palette mapping:
- 06:00–07:59 → SUNRISE (peach/orange)
- 08:00–16:59 → DAYLIGHT (blue)
- 17:00–17:59 → blend DAYLIGHT → NIGHT
- 18:00–05:59 → DAYLIGHT (because day side is collapsed/preview at night; this is intentional)

Hill palettes are **all greens** by design — the sun must be occluded by green hills at sunset, not a green→blue/orange transition. Don't reintroduce brown/teal hill colors.

## Broadcast patterns (add markup, get behavior)

JS uses `querySelectorAll` for these layers, so adding the matching DOM anywhere on the page wires it up automatically:

- `.fx-rain` / `.fx-snow` / `.fx-clouds-extra` / `.fx-fog` / `.fx-storm-tint` / `.fx-lightning` — `applyWeather()` broadcasts to all hosts. `host.closest('.bar-weather-fx')` is the "small host" check that scales particle count down (~18% for rain, ~25% for snow). Currently lives in: hero `.weather-fx`, every sticky bar `.bar-weather-fx`, footer `.bar-weather-fx`.
- `.shooting-stars` — `spawnShootingStar()` picks a random visible host each spawn. Currently in: hero, every night sticky bar, footer.
- `.moon-phase-shadow` — `applyMoonPhase()` writes `--moon-shadow-x` to `:root` so every moon on the page (hero, sticky bar mini-moon, etc.) updates from the same var.

## Sticky bar

Pattern: `position: fixed; transform: translateY(-100%)`, revealed by `transform: translateY(0)` when `body.hero-passed` (front page, after hero scrolls out) or `body.no-hero` (every other page) is set.

Layering inside the bar uses `isolation: isolate` + z-index:
- scenery (hills/city) — `z=0`
- bar-stars / shooting-stars / weather-fx — `z=1`
- bar-celestial / bar-logo / bar-page-title / bar-nav — `z=2` (`position: relative`)

Bar nav links share **all** styles with the home `.side-header .nav-link` via a joined selector — don't fork them. Active state = hover state (same selector group).

## Site footer

Fixed 150px height. `body.no-hero` is a flex column where `.content-block` grows and the footer shrinks (sticky-footer pattern) so it sits on the viewport bottom even on short posts.

Footer scenery uses the same broadcast pattern as the bar:
- Day: `.footer-hills` SVG (uses `var(--hill-*)` tokens) + `.footer-trees` (slim pine silhouettes with trunks)
- Night: `.footer-stars` field + empty `.shooting-stars` host (auto-populated by JS)
- Both sides: `.bar-weather-fx` host (clouds/fog/rain/snow/storm/lightning)

Per-side scenery is shown via `body[data-time="..."] .site-footer-scenery-..."]` — both day and night markup is always rendered.

## Body backgrounds (no-hero pages)

- Day post: solid `#f4f7fb` (matches `.panel-day`)
- Night post: solid `#0B0D1A` (matches `.panel-night`)

Sky tones are reserved for the bar at top and the footer scenery at bottom; the article body stays on the panel surface color.

## Debug toolbox

`?debug=1` on any URL adds `body.debug` and reveals the floating toolbox. Toolbox is rendered globally via `header.php`. Controls:
- Hour slider — front page only (hidden via `is_front_page()` check in PHP because side is locked on other pages)
- Moon phase slider — every page
- Weather override — every page

Both `if (hero)` and `if (!hero)` branches in main.js wire up the controls separately. Don't move declarations across the early-exit boundary without checking TDZ.

## main.js gotchas

- The IIFE has an `if (!hero) { ... return; }` early-exit for no-hero pages. **Any `const`/`let` referenced inside that block (like `lastApiCode`) must be declared above it** or you hit the temporal dead zone. Function declarations are hoisted, but `const`/`let` are not.
- `initCursorSaber()` is called BEFORE the early-exit so the lightsaber cursor works on every page. Don't move it back inside the front-page-only branch.
- `placeCelestials()` early-returns on mobile (`isMobileView()`) — the CSS centers the active celestial there. Always reset inline `style` on `.sun-wrap` / `.moon-wrap` before the early return so collapsed-mode CSS rules win on mobile.
- Synthetic mousemove events without `e.target.closest` will crash the cursor handler — always guard with `typeof e.target.closest === 'function'`.
- Catch-all `:not()` chains in `main.css` for `#day-side > *` / `#night-side > *` must include any new direct child you don't want absolutely positioned (e.g. `.side-footer` was lost twice from this list).

## Asset versioning

`inc/enqueue.php` uses `filemtime()` for the CSS/JS version string — every edit busts the browser cache automatically. Don't bump `FUNNIE_THEME_VERSION` for asset tweaks; just save the file.

## Local dev

`docker-compose.yml` runs WP locally. `bin/` and `deploy/` contain helper scripts (smoke test, seed, etc.). `.env.example` lists required env. There's no test suite — verify visually in the browser.
