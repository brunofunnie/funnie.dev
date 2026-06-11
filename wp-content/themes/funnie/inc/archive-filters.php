<?php
if (!defined('ABSPATH')) exit;

/**
 * Apply ?fy=YYYY&fm=MM filtering to category archives.
 *
 * Uses fy/fm (filter-year / filter-month) instead of year/month because
 * the latter are reserved WP query vars — passing ?year=2026 to a
 * category archive triggers redirect_canonical() to 301-redirect the
 * URL to /2026/ (WP's date archive), losing the category context.
 *
 * The archive UI ships year/month buttons that link back to the same
 * category archive URL with these params. We hook the main query here
 * so pagination, found_posts, and the archive title all see the
 * filtered set without archive.php having to build a custom WP_Query.
 */
add_action('pre_get_posts', function ($query) {
    if (is_admin() || !$query->is_main_query()) return;
    if (!$query->is_category()) return;

    $year  = isset($_GET['fy']) ? max(0, (int) $_GET['fy']) : 0;
    $month = isset($_GET['fm']) ? max(0, (int) $_GET['fm']) : 0;

    if (!$year && !$month) return;

    $date_query = [];
    if ($year)  $date_query['year']     = $year;
    if ($month) $date_query['monthnum'] = $month;
    $query->set('date_query', [$date_query]);
});

/**
 * List of (year, month, count) tuples for posts in the given category.
 * Used by archive.php to render the year/month filter sidebar — grouped
 * by year, newest first. Goes through $wpdb so we get a single SQL
 * round-trip instead of N WP_Query calls per period.
 */
function funnie_archive_periods(int $category_term_id): array {
    global $wpdb;

    $rows = $wpdb->get_results($wpdb->prepare(
        "SELECT YEAR(p.post_date) AS yr, MONTH(p.post_date) AS mo, COUNT(*) AS n
           FROM {$wpdb->posts} p
           INNER JOIN {$wpdb->term_relationships} tr ON tr.object_id = p.ID
           INNER JOIN {$wpdb->term_taxonomy} tt      ON tt.term_taxonomy_id = tr.term_taxonomy_id
          WHERE p.post_status = 'publish'
            AND p.post_type   = 'post'
            AND tt.taxonomy   = 'category'
            AND tt.term_id    = %d
          GROUP BY yr, mo
          ORDER BY yr DESC, mo DESC",
        $category_term_id
    ));

    $by_year = [];
    foreach ($rows as $r) {
        $yr = (int) $r->yr;
        $by_year[$yr][] = ['month' => (int) $r->mo, 'count' => (int) $r->n];
    }
    return $by_year;
}
