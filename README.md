# funnie.dev — WordPress site

Personal site for Bruno Oliveira (Funnie). Day/night split single-page layout with full-viewport panel overlays, served as a WordPress 6.9 site (PHP 8.3) with a Dockerised dev environment. Mirrors the operational shape of the `rimaraad` repo.

## Requirements

- Docker Desktop (or compatible Compose v2 runtime)
- GNU Make, curl, bash

## Quick start

```bash
cp .env.example .env     # only needed on first clone
make up                  # start containers
make seed                # install WP, activate theme, install ACF, run seed
make smoke               # verify the home page loads with the expected anchors
```

Then open:

| Surface    | URL                                            |
| ---------- | ---------------------------------------------- |
| Site       | http://localhost:8080                          |
| Admin      | http://localhost:8080/wp-admin (admin / admin) |
| phpMyAdmin | http://localhost:8081                          |
| Mailpit    | http://localhost:8025                          |

## Layout

```
├── bin/
│   ├── setup.sh    (install WP + plugins + theme, run seed)
│   ├── seed.php    (idempotent: ensure Home page + Site Settings singleton)
│   └── smoke.sh    (curl checks against the home page)
├── docker-compose.yml
├── Makefile
├── wp-content/
│   ├── mu-plugins/
│   │   ├── 000-smtp.php          (routes wp_mail to mailpit)
│   │   └── 001-dev-uploads.php   (SVG/WebP MIME)
│   └── themes/funnie/
│       ├── style.css                  (theme header)
│       ├── functions.php              (bootstrap)
│       ├── header.php / footer.php
│       ├── front-page.php             (renders the day/night layout)
│       ├── index.php / single.php / archive.php / search.php / 404.php
│       ├── inc/
│       │   ├── enqueue.php            (Google Fonts + Tailwind CDN + main.css/js)
│       │   ├── post-types.php         (Site Settings singleton)
│       │   ├── taxonomies.php         (stub)
│       │   ├── site-settings.php      (funnie_settings() helper)
│       │   ├── image-helpers.php      (funnie_image() ACF normalizer)
│       │   ├── parse-helpers.php      (funnie_parse_rows() pipe-textarea)
│       │   └── acf-fields.php         (per-panel ACF groups on Site Settings)
│       ├── template-parts/
│       │   ├── debug-box.php
│       │   ├── modals.php             (Discord modal)
│       │   ├── panel-{about,resume,hardware,blog,socials}.php
│       │   ├── hero.php               (day/night sides + sun/moon + scenery)
│       │   └── svg-symbols.php
│       └── assets/
│           ├── css/main.css   (verbatim copy of funnie.dev-template/styles.css)
│           ├── js/main.js     (verbatim copy of funnie.dev-template/script.js)
│           ├── avatar-bo.svg
│           ├── cursor.gif
│           └── cursor_pointer.gif
└── .env.example
```

## Editing the site

All home content lives on a singleton Site Settings post (`Admin → Site Settings → Funnie — Site Settings`), with one ACF group per panel:

- **Hero** — wordmark, DAY/NIGHT labels, footer taglines
- **About panel** — avatar, name, alias, bio paragraphs, stack chips
- **Resume panel** — intro, PDF URL, education
- **Hardware panel** — intro
- **Blog panel** — intro (cards come from native WP posts; the three placeholder posts in the layout are used as fallback when no posts are published)
- **Socials panel** — intros, Instagram/GitHub/Discord/email

Blog posts are native WP posts (`/wp-admin/edit.php`). Until any are published, the panel falls back to the three placeholder posts that ship with the layout.

## Theme development

Edit files under `wp-content/themes/funnie/` directly — the theme dir is bind-mounted into the WordPress container, so changes are live without a rebuild.

The styles + scripts come from the `funnie.dev-template/` static layout, copied verbatim into `assets/css/main.css` and `assets/js/main.js`. The Tailwind CDN with inline palette config is loaded via `inc/enqueue.php` so utility classes in the markup keep working.

## Mail

`wp_mail` is routed to Mailpit at http://localhost:8025 in dev.
