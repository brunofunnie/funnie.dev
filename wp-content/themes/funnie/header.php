<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php /* Day icon ships as the initial href; main.js swaps to night via #favicon when body[data-time] flips. */ ?>
    <link rel="icon" type="image/png" id="favicon" href="<?php echo esc_url(FUNNIE_THEME_URL . '/assets/funnie_day_ico.png'); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <?php wp_head(); ?>
</head>
<body <?php body_class('bg-day-bg text-day-text'); ?>>
<?php wp_body_open(); ?>
<?php get_template_part('template-parts/debug-box'); ?>
<a href="#hero" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 focus:rounded focus:bg-day-text focus:px-4 focus:py-2 focus:font-mono focus:text-xs focus:uppercase focus:tracking-[0.2em] focus:text-day-bg"><?php esc_html_e('Skip to content', 'funnie'); ?></a>
