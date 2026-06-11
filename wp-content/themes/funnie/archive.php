<?php
get_header();

$queried     = get_queried_object();
$day_cat     = funnie_settings('blog_day_category',   'professional');
$night_cat   = funnie_settings('blog_night_category', 'personal');

$current_cat_slug = (is_category() && isset($queried->slug)) ? $queried->slug : '';
$is_day_archive   = ($current_cat_slug === $day_cat);
$side             = $is_day_archive ? 'day' : 'night';

$logo_top    = funnie_settings('hero_logo_top',    'funnie');
$logo_bottom = funnie_settings('hero_logo_bottom', 'dev');
$home_url    = home_url('/');

$nav_items = $side === 'day'
    ? [['Home', '', $home_url], ['About', 'about', null], ['Blog', 'blog-day', null, true]]
    : [['Home', '', $home_url], ['Hardware', 'hardware', null], ['Blog', 'blog-night', null, true], ['Socials', 'socials', null]];

// Year/month filter state — these come from inc/archive-filters.php which
// already wired them into the main query via pre_get_posts. We just read
// them here to render the active state in the filter UI. Param names are
// fy/fm (not year/month) to avoid colliding with WP's reserved vars,
// which would otherwise trigger a canonical redirect to /YYYY/.
$active_year  = isset($_GET['fy']) ? max(0, (int) $_GET['fy']) : 0;
$active_month = isset($_GET['fm']) ? max(0, (int) $_GET['fm']) : 0;

$periods = [];
$base_archive_url = '';
if (is_category() && isset($queried->term_id)) {
    $periods = funnie_archive_periods((int) $queried->term_id);
    $term_link = get_term_link($queried);
    if (!is_wp_error($term_link)) $base_archive_url = $term_link;
}

$month_names = [
    1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun',
    7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec',
];

$filter_url = function (int $year = 0, int $month = 0) use ($base_archive_url): string {
    if (!$base_archive_url) return '#';
    $params = [];
    if ($year)  $params['fy'] = $year;
    if ($month) $params['fm'] = $month;
    return $params ? add_query_arg($params, $base_archive_url) : $base_archive_url;
};
?>
<script>document.body.dataset.time = '<?php echo esc_js($side); ?>';</script>

<div class="content-block content-block-<?php echo esc_attr($side); ?>" data-side="<?php echo esc_attr($side); ?>">

    <?php get_template_part('template-parts/sticky-bar', null, [
        'side'        => $side,
        'page_title'  => 'Archive',
        'home_url'    => $home_url,
        'logo_top'    => $logo_top,
        'logo_bottom' => $logo_bottom,
        'nav_items'   => $nav_items,
    ]); ?>

    <section class="panel panel-<?php echo esc_attr($side); ?>">
        <div class="panel-body">
            <header class="mb-10">
                <div class="font-mono text-xs uppercase tracking-[0.2em] text-<?php echo esc_attr($side); ?>-muted">// archive</div>
                <h1 class="mt-2 text-4xl font-bold tracking-tight md:text-5xl"><?php echo esc_html(single_cat_title('', false) ?: 'All posts'); ?></h1>
                <?php if ($active_year || $active_month): ?>
                    <p class="mt-3 font-mono text-xs uppercase tracking-[0.2em] text-<?php echo esc_attr($side); ?>-muted">
                        Filtered:
                        <?php echo esc_html(implode(' · ', array_filter([
                            $active_month ? $month_names[$active_month] : null,
                            $active_year  ? (string) $active_year       : null,
                        ]))); ?>
                        <a href="<?php echo esc_url($filter_url()); ?>" class="ml-2 text-<?php echo esc_attr($side); ?>-accent">clear</a>
                    </p>
                <?php endif; ?>
            </header>

            <div class="grid gap-10 md:grid-cols-[1fr,220px]">

                <div>
                    <?php if (have_posts()): ?>
                        <ul class="post-index border-y border-<?php echo esc_attr($side); ?>-border">
                            <?php while (have_posts()): the_post(); ?>
                                <li class="post-index-item border-t border-<?php echo esc_attr($side); ?>-border first:border-t-0">
                                    <a href="<?php the_permalink(); ?>" class="post-link flex flex-col gap-1 px-3 py-4 md:flex-row md:items-baseline md:justify-between md:gap-6">
                                        <span class="post-link-title text-base font-medium"><?php the_title(); ?></span>
                                        <time datetime="<?php echo esc_attr(get_the_date('c')); ?>" class="post-link-date shrink-0 font-mono text-xs uppercase tracking-[0.2em] text-<?php echo esc_attr($side); ?>-muted"><?php echo esc_html(get_the_date('Y-m-d')); ?></time>
                                    </a>
                                </li>
                            <?php endwhile; ?>
                        </ul>
                        <nav class="archive-pagination mt-10" aria-label="<?php esc_attr_e('Pagination', 'funnie'); ?>">
                            <?php the_posts_pagination([
                                'prev_text'          => '‹',
                                'next_text'          => '›',
                                'mid_size'           => 1,
                                'screen_reader_text' => __('Pagination', 'funnie'),
                            ]); ?>
                        </nav>
                    <?php else: ?>
                        <p class="text-<?php echo esc_attr($side); ?>-muted"><em><?php esc_html_e('Nothing posted in this period.', 'funnie'); ?></em></p>
                    <?php endif; ?>
                </div>

                <?php if (!empty($periods)): ?>
                    <aside class="archive-filter" aria-label="<?php esc_attr_e('Filter by date', 'funnie'); ?>">
                        <div class="mb-3 font-mono text-xs uppercase tracking-[0.2em] text-<?php echo esc_attr($side); ?>-muted">// when</div>
                        <ul class="space-y-4">
                            <?php foreach ($periods as $year => $months):
                                $year_active  = ($active_year === (int) $year && !$active_month);
                                $year_in_view = ($active_year === (int) $year);
                            ?>
                                <li>
                                    <a href="<?php echo esc_url($filter_url((int) $year)); ?>" class="block font-mono text-sm font-bold tracking-[0.15em] text-<?php echo esc_attr($side); ?>-text transition hover:text-<?php echo esc_attr($side); ?>-accent<?php echo $year_active ? ' text-' . esc_attr($side) . '-accent' : ''; ?>">
                                        <?php echo esc_html((string) $year); ?>
                                    </a>
                                    <ul class="mt-2 space-y-1 pl-3">
                                        <?php foreach ($months as $m):
                                            $is_active = ($active_year === (int) $year && $active_month === $m['month']);
                                        ?>
                                            <li>
                                                <a href="<?php echo esc_url($filter_url((int) $year, $m['month'])); ?>" class="flex items-baseline justify-between gap-3 font-mono text-xs uppercase tracking-[0.2em] transition hover:text-<?php echo esc_attr($side); ?>-accent <?php echo $is_active ? 'text-' . esc_attr($side) . '-accent' : 'text-' . esc_attr($side) . '-muted'; ?>">
                                                    <span><?php echo esc_html($month_names[$m['month']]); ?></span>
                                                    <span class="text-<?php echo esc_attr($side); ?>-muted"><?php echo esc_html((string) $m['count']); ?></span>
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </aside>
                <?php endif; ?>
            </div>
        </div>
    </section>
</div>

<?php get_footer(); ?>
