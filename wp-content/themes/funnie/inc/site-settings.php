<?php
if (!defined('ABSPATH')) exit;

function funnie_site_settings_id(): int {
    $cached = wp_cache_get('funnie_site_settings_id', 'funnie');
    if ($cached !== false) return (int) $cached;

    $q = new WP_Query([
        'post_type'      => 'site_settings',
        'post_status'    => ['publish', 'draft', 'private'],
        'posts_per_page' => 1,
        'fields'         => 'ids',
        'no_found_rows'  => true,
    ]);
    $id = $q->posts[0] ?? 0;
    wp_cache_set('funnie_site_settings_id', (int) $id, 'funnie', MINUTE_IN_SECONDS);
    return (int) $id;
}

function funnie_settings($key, $default = null) {
    $id = funnie_site_settings_id();
    if (!$id) return $default;
    $val = function_exists('get_field') ? get_field($key, $id) : null;
    return ($val !== null && $val !== '') ? $val : $default;
}

add_action('save_post_site_settings', function () {
    wp_cache_delete('funnie_site_settings_id', 'funnie');
});
