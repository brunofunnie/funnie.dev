<?php
/**
 * Funnie — theme bootstrap.
 */

if (!defined('ABSPATH')) exit;

define('FUNNIE_THEME_DIR', get_stylesheet_directory());
define('FUNNIE_THEME_URL', get_stylesheet_directory_uri());
define('FUNNIE_THEME_VERSION', '0.1.0');

require_once FUNNIE_THEME_DIR . '/inc/enqueue.php';
require_once FUNNIE_THEME_DIR . '/inc/post-types.php';
require_once FUNNIE_THEME_DIR . '/inc/taxonomies.php';
require_once FUNNIE_THEME_DIR . '/inc/site-settings.php';
require_once FUNNIE_THEME_DIR . '/inc/image-helpers.php';
require_once FUNNIE_THEME_DIR . '/inc/parse-helpers.php';

add_action('after_setup_theme', function () {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', ['search-form', 'gallery', 'caption', 'script', 'style']);
    register_nav_menus([
        'primary' => __('Primary navigation', 'funnie'),
        'footer'  => __('Footer',              'funnie'),
    ]);
});

add_action('acf/init', function () {
    require_once FUNNIE_THEME_DIR . '/inc/acf-fields.php';
});

add_action('admin_notices', function () {
    if (!class_exists('ACF') && !function_exists('acf_add_local_field_group')) {
        echo '<div class="notice notice-error"><p><strong>Funnie:</strong> ACF (or SCF) is not installed. Run <code>make seed</code> or install the plugin manually.</p></div>';
    }
});
