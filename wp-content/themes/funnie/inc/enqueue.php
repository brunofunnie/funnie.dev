<?php
if (!defined('ABSPATH')) exit;

add_action('wp_enqueue_scripts', function () {

    wp_enqueue_style(
        'funnie-google-fonts',
        'https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;700&family=Space+Mono:wght@400;700&display=swap',
        [],
        null
    );

    // Use filemtime() so every edit busts the browser cache during dev — the
    // theme version alone never changes between CSS/JS tweaks.
    $css_path = FUNNIE_THEME_DIR . '/assets/css/main.css';
    $js_path  = FUNNIE_THEME_DIR . '/assets/js/main.js';

    wp_enqueue_style(
        'funnie-main',
        FUNNIE_THEME_URL . '/assets/css/main.css',
        ['funnie-google-fonts'],
        file_exists($css_path) ? (string) filemtime($css_path) : FUNNIE_THEME_VERSION
    );

    wp_enqueue_script(
        'funnie-main',
        FUNNIE_THEME_URL . '/assets/js/main.js',
        [],
        file_exists($js_path) ? (string) filemtime($js_path) : FUNNIE_THEME_VERSION,
        true
    );

    wp_localize_script('funnie-main', 'FUNNIE', [
        'homeUrl'  => home_url('/'),
        'themeUrl' => FUNNIE_THEME_URL,
        'dayIcon'   => FUNNIE_THEME_URL . '/assets/funnie_day_ico.png',
        'nightIcon' => FUNNIE_THEME_URL . '/assets/funnie_night_ico.png',
    ]);
}, 20);

/**
 * Tailwind CDN runtime — kept because the layout uses utility classes inline
 * (e.g. flex-1, font-mono, grid-cols-2). The custom palette/font config is
 * inlined via wp_head action so it loads before the CDN script (Play CDN
 * convention).
 */
add_action('wp_head', function () {
    ?>
    <script>
      window.tailwind = window.tailwind || {};
      tailwind.config = {
        theme: {
          extend: {
            fontFamily: {
              sans: ['"Space Grotesk"', 'system-ui', 'sans-serif'],
              mono: ['"Space Mono"', 'ui-monospace', 'monospace'],
            },
            colors: {
              day: {
                bg: '#f4f7fb',
                surface: '#ffffff',
                accent: '#7cb6ff',
                'accent-warm': '#ffd58a',
                text: '#0f1b2d',
                muted: '#5b6b82',
                border: '#dbe4f0',
              },
              night: {
                bg: '#0a0d1a',
                surface: '#11162b',
                accent: '#a78bfa',
                'accent-cool': '#6ee7ff',
                text: '#e8ecff',
                muted: '#8a90b8',
                border: '#222743',
              },
            },
          },
        },
      };
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <?php
}, 5);
