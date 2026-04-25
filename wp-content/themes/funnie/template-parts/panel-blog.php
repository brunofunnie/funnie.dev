<?php
if (!defined('ABSPATH')) exit;

/**
 * Args (passed via get_template_part('template-parts/panel-blog', null, [...])):
 *   panel_id      string  HTML id for the <aside> (e.g. 'panel-blog-day')
 *   data_side     string  'day' or 'night' — drives panel-day / panel-night class
 *   category      string  WP category slug to filter posts by ('' = no filter)
 *   intro_default string  Fallback intro line if no ACF setting is set
 *   intro_key     string  ACF key for the intro on Site Settings
 *   placeholders  array   Fallback posts when WP_Query returns nothing (id => [date,title,excerpt,body])
 */
$args = wp_parse_args($args ?? [], [
    'panel_id'      => 'panel-blog',
    'data_side'     => 'night',
    'category'      => '',
    'intro_default' => 'Notes from the night side. Web development, hardware, the occasional aside.',
    'intro_key'     => 'blog_intro',
    'placeholders'  => [],
]);

$intro     = funnie_settings($args['intro_key'], $args['intro_default']);
$panel_id  = $args['panel_id'];
$side      = $args['data_side'];
$title_id  = $panel_id . '-title';

$query_args = [
    'post_type'           => 'post',
    'post_status'         => 'publish',
    'posts_per_page'      => 6,
    'ignore_sticky_posts' => true,
    'no_found_rows'       => true,
];
if ($args['category'] !== '') {
    $query_args['category_name'] = $args['category'];
}

$blog_query = new WP_Query($query_args);

$posts_data = [];
if ($blog_query->have_posts()) {
    while ($blog_query->have_posts()) {
        $blog_query->the_post();
        $id = (string) get_the_ID();
        $posts_data[$id] = [
            'date'    => get_the_date('Y-m-d'),
            'title'   => get_the_title(),
            'excerpt' => wp_strip_all_tags(get_the_excerpt()),
        ];
    }
    wp_reset_postdata();
} else {
    $posts_data = $args['placeholders'];
}

$muted_class = 'text-' . $side . '-muted';
$accent_class = 'text-' . $side . '-accent';
$border_class = 'border-' . $side . '-border';
$surface_class = 'bg-' . $side . '-surface';
?>
<aside id="<?php echo esc_attr($panel_id); ?>" class="panel panel-<?php echo esc_attr($side); ?>" data-side="<?php echo esc_attr($side); ?>" data-blog-panel role="dialog" aria-modal="true" aria-labelledby="<?php echo esc_attr($title_id); ?>" hidden>
    <button type="button" class="panel-close" aria-label="Close Blog panel">×</button>
    <h2 id="<?php echo esc_attr($title_id); ?>" class="panel-title">Blog</h2>
    <div class="panel-body" data-blog-view="list">

        <div data-blog-section="list">
            <p class="mb-8 max-w-prose <?php echo esc_attr($muted_class); ?>"><?php echo esc_html($intro); ?></p>
            <?php if (!empty($posts_data)): ?>
                <ul class="space-y-4">
                    <?php foreach ($posts_data as $id => $p): ?>
                        <li>
                            <button type="button" data-blog-open="<?php echo esc_attr($id); ?>" class="block w-full rounded-lg border <?php echo esc_attr($border_class . ' ' . $surface_class); ?> p-5 text-left transition hover:-translate-y-1 hover:shadow-[0_0_30px_rgba(167,139,250,0.25)]">
                                <div class="font-mono text-xs uppercase tracking-[0.2em] <?php echo esc_attr($muted_class); ?>"><?php echo esc_html($p['date']); ?></div>
                                <div class="mt-1 text-lg font-bold"><?php echo esc_html($p['title']); ?></div>
                                <p class="mt-2 text-sm <?php echo esc_attr($muted_class); ?>"><?php echo esc_html($p['excerpt']); ?></p>
                                <span class="mt-3 inline-block font-mono text-xs uppercase tracking-[0.2em] <?php echo esc_attr($accent_class); ?>">Read →</span>
                            </button>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p class="<?php echo esc_attr($muted_class); ?>"><em>Nothing posted here yet.</em></p>
            <?php endif; ?>
        </div>

        <article data-blog-section="detail" hidden>
            <button type="button" data-blog-back class="mb-6 font-mono text-xs uppercase tracking-[0.2em] <?php echo esc_attr($accent_class); ?>">← Back to posts</button>
            <div data-blog-detail-content></div>
        </article>

        <script type="application/json" data-blog-posts data-rest-url="<?php echo esc_url(rest_url('wp/v2/posts')); ?>">
        <?php
        $json_payload = [];
        foreach ($posts_data as $id => $p) {
            if (!empty($p['body'])) {
                $json_payload[$id] = ['date' => $p['date'], 'title' => $p['title'], 'body' => $p['body']];
            }
        }
        echo wp_json_encode($json_payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_FORCE_OBJECT);
        ?>
        </script>

    </div>
</aside>
