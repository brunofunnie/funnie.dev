<?php
if (!defined('ABSPATH')) exit;

/**
 * Args (passed via get_template_part('template-parts/sticky-bar', null, [...])):
 *   side       string  'day' or 'night'
 *   page_title string  Section label shown next to the logo (e.g. 'Blog', 'Archive')
 *   home_url   string  Where the logo / celestial should link to
 *   logo_top   string  Logo top line text
 *   logo_bottom string Logo bottom line text
 *   nav_items  array   [[label, anchor, url|null, is_current?], ...]
 *                      url=null falls back to {home_url}#{anchor}
 */
$args = wp_parse_args($args ?? [], [
    'side'        => 'night',
    'page_title'  => '',
    'home_url'    => home_url('/'),
    'logo_top'    => 'funnie',
    'logo_bottom' => 'dev',
    'nav_items'   => [],
]);
$side       = $args['side'];
$page_title = $args['page_title'];
$home_url   = $args['home_url'];
?>
<header class="sticky-bar sticky-bar-<?php echo esc_attr($side); ?>" aria-label="<?php echo $side === 'day' ? 'Day' : 'Night'; ?> navigation">
    <div class="bar-scenery" aria-hidden="true">
        <?php if ($side === 'day'): ?>
            <svg class="bar-hills" viewBox="0 0 1200 90" preserveAspectRatio="none" fill="none">
                <path d="M0,55 C160,30 320,55 500,40 C680,25 840,55 1000,38 C1120,28 1170,42 1200,40 L1200,90 L0,90 Z" fill="var(--hill-far)"/>
                <path d="M0,68 C200,50 350,68 520,58 C680,48 820,65 980,55 C1100,48 1160,60 1200,58 L1200,90 L0,90 Z" fill="var(--hill-mid)"/>
                <path d="M0,80 C160,72 320,80 500,76 C680,72 840,79 1000,75 C1120,72 1170,77 1200,76 L1200,90 L0,90 Z" fill="var(--hill-near)"/>
            </svg>
        <?php else: ?>
            <svg class="bar-city" viewBox="0 0 1200 90" preserveAspectRatio="none" fill="none">
                <g fill="#040813">
                    <rect x="0"    y="62" width="80"  height="28"/>
                    <rect x="80"   y="50" width="60"  height="40"/>
                    <rect x="140"  y="68" width="40"  height="22"/>
                    <rect x="180"  y="42" width="80"  height="48"/>
                    <rect x="260"  y="58" width="50"  height="32"/>
                    <rect x="310"  y="48" width="70"  height="42"/>
                    <rect x="380"  y="64" width="40"  height="26"/>
                    <rect x="420"  y="38" width="90"  height="52"/>
                    <rect x="510"  y="56" width="60"  height="34"/>
                    <rect x="570"  y="46" width="80"  height="44"/>
                    <rect x="650"  y="62" width="50"  height="28"/>
                    <rect x="700"  y="40" width="100" height="50"/>
                    <rect x="800"  y="60" width="60"  height="30"/>
                    <rect x="860"  y="48" width="80"  height="42"/>
                    <rect x="940"  y="66" width="40"  height="24"/>
                    <rect x="980"  y="44" width="90"  height="46"/>
                    <rect x="1070" y="58" width="60"  height="32"/>
                    <rect x="1130" y="50" width="70"  height="40"/>
                </g>
                <g fill="#ffd66e" opacity="0.55">
                    <rect x="30"   y="76" width="2" height="2"/>
                    <rect x="100"  y="68" width="2" height="2"/>
                    <rect x="200"  y="60" width="2" height="2"/>
                    <rect x="330"  y="64" width="2" height="2"/>
                    <rect x="450"  y="56" width="2" height="2"/>
                    <rect x="600"  y="60" width="2" height="2"/>
                    <rect x="730"  y="56" width="2" height="2"/>
                    <rect x="900"  y="62" width="2" height="2"/>
                    <rect x="1010" y="58" width="2" height="2"/>
                    <rect x="1160" y="64" width="2" height="2"/>
                </g>
            </svg>
        <?php endif; ?>
    </div>
    <?php if ($side === 'night'): ?>
        <div class="bar-stars" aria-hidden="true">
            <span class="star" style="top:18%;left:8%;width:1.5px;height:1.5px;animation-delay:0s"></span>
            <span class="star" style="top:30%;left:22%;width:2px;height:2px;animation-delay:0.6s"></span>
            <span class="star" style="top:14%;left:38%;width:1px;height:1px;animation-delay:1.2s"></span>
            <span class="star" style="top:42%;left:54%;width:2px;height:2px;animation-delay:0.3s"></span>
            <span class="star" style="top:22%;left:68%;width:1.5px;height:1.5px;animation-delay:1.8s"></span>
            <span class="star" style="top:34%;left:82%;width:2.5px;height:2.5px;animation-delay:0.9s"></span>
            <span class="star" style="top:16%;left:92%;width:1px;height:1px;animation-delay:2.4s"></span>
        </div>
        <div class="shooting-stars" aria-hidden="true"></div>
    <?php endif; ?>
    <div class="bar-weather-fx" aria-hidden="true">
        <div class="fx-clouds-extra"></div>
        <div class="fx-rain"></div>
        <div class="fx-snow"></div>
        <div class="fx-fog"></div>
        <div class="fx-storm-tint"></div>
        <div class="fx-lightning"></div>
    </div>
    <a class="bar-celestial bar-<?php echo $side === 'day' ? 'sun' : 'moon'; ?>" href="<?php echo esc_url($home_url); ?>" aria-label="Back home">
        <?php if ($side === 'day'): ?>
            <span class="sun-mini" aria-hidden="true"></span>
        <?php else: ?>
            <span class="moon-mini" aria-hidden="true"><span class="moon-phase-shadow"></span></span>
        <?php endif; ?>
    </a>
    <a class="bar-logo" href="<?php echo esc_url($home_url); ?>">
        <span class="bar-logo-line"><?php echo esc_html($args['logo_top']); ?></span>
        <span class="bar-logo-line"><?php echo esc_html($args['logo_bottom']); ?></span>
    </a>
    <?php if ($page_title !== ''): ?>
        <span class="bar-page-title" aria-hidden="true"><?php echo esc_html($page_title); ?></span>
    <?php endif; ?>
    <nav class="bar-nav" aria-label="<?php echo $side === 'day' ? 'Day sections' : 'Night sections'; ?>">
        <?php foreach ($args['nav_items'] as $item):
            $label   = $item[0];
            $anchor  = $item[1];
            $href    = $item[2] ?? null;
            $current = !empty($item[3]);
            $url     = $href !== null ? $href : ($home_url . '#' . $anchor);
        ?>
            <a href="<?php echo esc_url($url); ?>" class="nav-link <?php echo esc_attr($side); ?>-nav<?php echo $current ? ' is-current' : ''; ?>"<?php echo $current ? ' aria-current="page"' : ''; ?>><?php echo esc_html($label); ?></a>
        <?php endforeach; ?>
    </nav>
    <div class="bar-weather weather" data-weather aria-live="polite">
        <span class="weather-icon" aria-hidden="true">·</span>
        <span class="weather-temp">--°</span>
        <span class="weather-condition">…</span>
        <span class="weather-location">Sorocaba, BR</span>
    </div>
</header>
