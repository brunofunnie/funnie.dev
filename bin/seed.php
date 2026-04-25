<?php
/**
 * Funnie — minimal seeder.
 *
 * Idempotent: ensures a Home page exists and is set as the static front page,
 * and creates the singleton Site Settings post if missing.
 */

if (!defined('ABSPATH')) exit;

WP_CLI::log("→ Ensuring Home page exists…");
$home = get_page_by_path('home');
if (!$home) {
    $home_id = wp_insert_post([
        'post_type'    => 'page',
        'post_status'  => 'publish',
        'post_title'   => 'Home',
        'post_name'    => 'home',
        'post_content' => '',
    ]);
    WP_CLI::log("  ✓ Created Home page (ID {$home_id})");
} else {
    $home_id = $home->ID;
    WP_CLI::log("  · Home page already exists (ID {$home_id})");
}

update_option('show_on_front', 'page');
update_option('page_on_front', $home_id);

WP_CLI::log("→ Ensuring Site Settings singleton…");
$settings_q = new WP_Query([
    'post_type'      => 'site_settings',
    'post_status'    => ['publish', 'draft', 'private'],
    'posts_per_page' => 1,
    'fields'         => 'ids',
    'no_found_rows'  => true,
]);
if (empty($settings_q->posts)) {
    wp_insert_post([
        'post_type'   => 'site_settings',
        'post_status' => 'publish',
        'post_title'  => 'Funnie — Site Settings',
    ]);
    WP_CLI::log("  ✓ Created Site Settings post");
} else {
    WP_CLI::log("  · Site Settings post already exists");
}

WP_CLI::log("→ Ensuring blog categories…");
foreach ([
    'professional' => 'Professional',
    'personal'     => 'Personal',
] as $slug => $name) {
    if (get_term_by('slug', $slug, 'category')) {
        WP_CLI::log("  · Category '{$slug}' already exists");
        continue;
    }
    $res = wp_insert_term($name, 'category', ['slug' => $slug]);
    if (is_wp_error($res)) {
        WP_CLI::warning("  ✗ Failed to create '{$slug}': " . $res->get_error_message());
    } else {
        WP_CLI::log("  ✓ Created category '{$slug}'");
    }
}

WP_CLI::log("→ Pretty permalinks…");
update_option('permalink_structure', '/%postname%/');
flush_rewrite_rules(false);

WP_CLI::success("Seed complete.");
