<?php
/**
 * Funnie — minimal seeder.
 *
 * Idempotent: ensures a Home page exists and is set as the static front page,
 * the singleton Site Settings post exists, blog categories exist, pretty
 * permalinks are on, and the two "launch" blog posts (day + night) exist with
 * their featured images. Any other posts in the database are removed so the
 * site ships in a known state on every deploy.
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

/* ---------------------------------------------------------------------------
 * Blog seeding — launch posts (day + night).
 *
 * The site has two parallel blog feeds, one per "side". The launch post has
 * the same body on both sides; only the featured image and category change.
 * Existing posts that don't match a seeded slug are removed so deploys land
 * in a deterministic state. Edits made through wp-admin to the seeded posts
 * themselves are preserved (we match by slug — present means leave alone).
 * ------------------------------------------------------------------------- */

WP_CLI::log("→ Seeding launch blog posts…");

$launch_body = <<<'HTML'
<p>After years of meaning to, I finally put up a personal site. Two sides — a day for the professional notes and a night for the rest — and a single front page that does its best to feel alive.</p>
<p>This is where I'll write about web development, AI-assisted workflows, the hardware on my desk, and whatever else feels worth a paragraph. Less polished than a magazine, more honest than a LinkedIn post.</p>
<p>If you're hiring, working on a project that needs hands, or just want to talk about something I've written here — the socials at the bottom of the page are real and the inbox is open. I'd genuinely love to hear from you.</p>
HTML;

$launch_excerpt = "A small place of my own to share what I'm building, what I'm reading, and the occasional aside. Open to work, open to conversation.";

/* Use a timestamp slightly in the past so wp_insert_post does not flip the
 * status to 'future' (which silently hides the post from the public query
 * and made early seed runs look like nothing was created). */
$now_ts     = current_time('U');
$day_date   = wp_date('Y-m-d H:i:s', $now_ts - 7200, wp_timezone());
$night_date = wp_date('Y-m-d H:i:s', $now_ts - 3600, wp_timezone());

$launch_posts = [
    [
        'slug'        => 'hello-day-side',
        'title'       => 'Hello — the site is live.',
        'date'        => $day_date,
        'category'    => 'professional',
        'image'       => 'assets/images/blog/launch-day.png',
        'image_title' => 'Funnie · day side',
    ],
    [
        'slug'        => 'hello-night-side',
        'title'       => 'Hello — the site is live.',
        'date'        => $night_date,
        'category'    => 'personal',
        'image'       => 'assets/images/blog/launch-night.png',
        'image_title' => 'Funnie · night side',
    ],
];

$keep_slugs = array_column($launch_posts, 'slug');

/* Explicit status list — 'any' silently excludes 'future' (because that
 * status has exclude_from_search=true), which would let scheduled-but-
 * never-published posts slip past the cleanup. */
$all_statuses = ['publish', 'future', 'draft', 'pending', 'private'];

/* Drop every post that isn't part of the seed so the live database mirrors
 * the seed (handles WP's default "Hello world!" sample, leftover demo posts,
 * etc.). Force-delete (skip Trash) so the URL slugs are fully released. */
$existing_posts = get_posts([
    'post_type'        => 'post',
    'post_status'      => $all_statuses,
    'numberposts'      => -1,
    'suppress_filters' => true,
]);
foreach ($existing_posts as $existing) {
    if (in_array($existing->post_name, $keep_slugs, true)) continue;
    wp_delete_post($existing->ID, true);
    WP_CLI::log("  ✗ Removed stale post '{$existing->post_name}' (ID {$existing->ID})");
}

/**
 * Sideload an image from the active theme directory into the media library
 * and return its attachment ID. Idempotent — reuses an existing attachment
 * matched by post_title so re-runs do not pile up duplicates in uploads/.
 */
$funnie_attach_image = function (string $relative_path, string $title): int {
    $existing = get_posts([
        'post_type'   => 'attachment',
        'title'       => $title,
        'numberposts' => 1,
        'fields'      => 'ids',
    ]);
    if (!empty($existing)) return (int) $existing[0];

    $src = trailingslashit(get_stylesheet_directory()) . ltrim($relative_path, '/');
    if (!file_exists($src)) {
        WP_CLI::warning("  ✗ Image not found: {$src}");
        return 0;
    }

    $upload = wp_upload_dir();
    if (!empty($upload['error'])) {
        WP_CLI::warning("  ✗ Upload dir error: {$upload['error']}");
        return 0;
    }

    $filename = wp_unique_filename($upload['path'], basename($src));
    $dest     = trailingslashit($upload['path']) . $filename;

    if (!@copy($src, $dest)) {
        WP_CLI::warning("  ✗ Failed to copy {$src} → {$dest}");
        return 0;
    }

    $filetype = wp_check_filetype($filename, null);
    $attach_id = wp_insert_attachment([
        'post_mime_type' => $filetype['type'] ?: 'image/png',
        'post_title'     => $title,
        'post_content'   => '',
        'post_status'    => 'inherit',
    ], $dest);

    if (is_wp_error($attach_id) || !$attach_id) {
        WP_CLI::warning("  ✗ wp_insert_attachment failed for {$dest}");
        return 0;
    }

    require_once ABSPATH . 'wp-admin/includes/image.php';
    wp_update_attachment_metadata($attach_id, wp_generate_attachment_metadata($attach_id, $dest));

    return (int) $attach_id;
};

foreach ($launch_posts as $post_data) {
    $existing = get_posts([
        'name'        => $post_data['slug'],
        'post_type'   => 'post',
        'post_status' => $all_statuses,
        'numberposts' => 1,
    ]);
    if (!empty($existing)) {
        $found = $existing[0];
        /* Heal a previously-seeded post that ended up with the wrong status
         * (e.g. 'future' from an earlier seed run with a future post_date).
         * Force it to publish + a past date so it appears on the front page. */
        if ($found->post_status !== 'publish') {
            wp_update_post([
                'ID'            => $found->ID,
                'post_status'   => 'publish',
                'post_date'     => $post_data['date'],
                'post_date_gmt' => get_gmt_from_date($post_data['date']),
            ]);
            WP_CLI::log("  ↻ Republished post '{$post_data['slug']}' (ID {$found->ID}, was '{$found->post_status}')");
        } else {
            WP_CLI::log("  · Post '{$post_data['slug']}' already exists (ID {$found->ID})");
        }
        continue;
    }

    $cat_term = get_term_by('slug', $post_data['category'], 'category');
    $cat_ids  = $cat_term ? [(int) $cat_term->term_id] : [];

    $post_id = wp_insert_post([
        'post_type'     => 'post',
        'post_status'   => 'publish',
        'post_title'    => $post_data['title'],
        'post_name'     => $post_data['slug'],
        'post_excerpt'  => $launch_excerpt,
        'post_content'  => $launch_body,
        'post_date'     => $post_data['date'],
        'post_category' => $cat_ids,
    ], true);

    if (is_wp_error($post_id) || !$post_id) {
        $msg = is_wp_error($post_id) ? $post_id->get_error_message() : 'unknown error';
        WP_CLI::warning("  ✗ Failed to create '{$post_data['slug']}': {$msg}");
        continue;
    }

    $attach_id = $funnie_attach_image($post_data['image'], $post_data['image_title']);
    if ($attach_id) {
        set_post_thumbnail($post_id, $attach_id);
    }

    WP_CLI::log("  ✓ Created post '{$post_data['slug']}' (ID {$post_id})" . ($attach_id ? " · thumb {$attach_id}" : ''));
}

WP_CLI::success("Seed complete.");
