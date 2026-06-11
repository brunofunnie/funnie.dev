<?php
if (!defined('ABSPATH')) exit;

/**
 * Args (passed via get_template_part('template-parts/panel-blog', null, [...])):
 *   panel_id      string  HTML id for the <aside> (e.g. 'panel-blog-day')
 *   data_side     string  'day' or 'night' — drives panel-day / panel-night class
 *   category      string  WP category slug to filter posts by ('' = no filter)
 *   intro_default string  Fallback intro line if no ACF setting is set
 *   intro_key     string  ACF key for the intro on Site Settings
 */
$args = wp_parse_args($args ?? [], [
    'panel_id'      => 'panel-blog',
    'data_side'     => 'night',
    'category'      => '',
    'intro_default' => 'Notes from the night side. Web development, hardware, the occasional aside.',
    'intro_key'     => 'blog_intro',
]);

$intro     = funnie_settings($args['intro_key'], $args['intro_default']);
$panel_id  = $args['panel_id'];
$side      = $args['data_side'];
$title_id  = $panel_id . '-title';

$query_args = [
    'post_type'           => 'post',
    'post_status'         => 'publish',
    'posts_per_page'      => 10,
    'ignore_sticky_posts' => true,
    'no_found_rows'       => true,
];
if ($args['category'] !== '') {
    $query_args['category_name'] = $args['category'];
}

$blog_query = new WP_Query($query_args);

$archive_url = '';
if ($args['category'] !== '') {
    $cat_term = get_term_by('slug', $args['category'], 'category');
    if ($cat_term && !is_wp_error($cat_term)) {
        $archive_url = get_term_link($cat_term);
        if (is_wp_error($archive_url)) $archive_url = '';
    }
}

$muted_class   = 'text-' . $side . '-muted';
$accent_class  = 'text-' . $side . '-accent';
$border_class  = 'border-' . $side . '-border';
$surface_class = 'bg-' . $side . '-surface';
?>
<section id="<?php echo esc_attr($panel_id); ?>" class="panel panel-<?php echo esc_attr($side); ?>" data-side="<?php echo esc_attr($side); ?>" data-blog-panel aria-labelledby="<?php echo esc_attr($title_id); ?>">
    <h2 id="<?php echo esc_attr($title_id); ?>" class="panel-title">Blog</h2>
    <div class="panel-body">
        <p class="mb-8 max-w-prose <?php echo esc_attr($muted_class); ?>"><?php echo esc_html($intro); ?></p>

        <?php if ($blog_query->have_posts()): ?>
            <ul class="post-index border-y <?php echo esc_attr($border_class); ?>">
                <?php while ($blog_query->have_posts()): $blog_query->the_post(); ?>
                    <li class="post-index-item border-t <?php echo esc_attr($border_class); ?> first:border-t-0">
                        <a href="<?php the_permalink(); ?>" class="post-link flex flex-col gap-1 px-3 py-4 md:flex-row md:items-baseline md:justify-between md:gap-6">
                            <span class="post-link-title text-base font-medium"><?php the_title(); ?></span>
                            <time datetime="<?php echo esc_attr(get_the_date('c')); ?>" class="post-link-date shrink-0 font-mono text-xs uppercase tracking-[0.2em] <?php echo esc_attr($muted_class); ?>"><?php echo esc_html(get_the_date('Y-m-d')); ?></time>
                        </a>
                    </li>
                <?php endwhile; ?>
            </ul>
            <?php wp_reset_postdata(); ?>

            <?php if ($archive_url): ?>
                <div class="mt-6">
                    <a href="<?php echo esc_url($archive_url); ?>" class="inline-flex items-center gap-2 rounded-lg border <?php echo esc_attr($border_class . ' ' . $surface_class); ?> px-4 py-2 font-mono text-xs uppercase tracking-[0.2em] transition hover:border-<?php echo esc_attr($side); ?>-accent">More posts →</a>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <p class="<?php echo esc_attr($muted_class); ?>"><em>Nothing posted here yet.</em></p>
        <?php endif; ?>
    </div>
</section>
