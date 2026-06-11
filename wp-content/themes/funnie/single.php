<?php
get_header();

$post_id     = get_queried_object_id();
$day_cat     = funnie_settings('blog_day_category',   'professional');
$night_cat   = funnie_settings('blog_night_category', 'personal');
$slugs       = wp_get_post_categories($post_id, ['fields' => 'slugs']);
$is_day_post = in_array($day_cat,   $slugs, true);
// Posts not in the day category default to night so the night palette stays
// the safe fallback even if a post is mis-categorized.
$side        = $is_day_post ? 'day' : 'night';

$logo_top    = funnie_settings('hero_logo_top',    'funnie');
$logo_bottom = funnie_settings('hero_logo_bottom', 'dev');
$home_url    = home_url('/');
// Nav for the post page sticky bar. Home is always first; the Blog item is
// flagged so it can render with aria-current and an active style (the user
// is currently on a post under the Blog section).
$nav_items   = $side === 'day'
    ? [['Home', '', $home_url], ['About', 'about', null], ['Blog', 'blog-day', null, true]]
    : [['Home', '', $home_url], ['Hardware', 'hardware', null], ['Blog', 'blog-night', null, true], ['Socials', 'socials', null]];
?>
<script>document.body.dataset.time = '<?php echo esc_js($side); ?>';</script>

<div class="content-block content-block-<?php echo esc_attr($side); ?>" data-side="<?php echo esc_attr($side); ?>">

    <?php get_template_part('template-parts/sticky-bar', null, [
        'side'        => $side,
        'page_title'  => 'Blog',
        'home_url'    => $home_url,
        'logo_top'    => $logo_top,
        'logo_bottom' => $logo_bottom,
        'nav_items'   => $nav_items,
    ]); ?>

    <?php while (have_posts()): the_post(); ?>
        <article class="panel panel-<?php echo esc_attr($side); ?>">
            <div class="panel-body">
                <?php if (has_post_thumbnail()): ?>
                    <figure class="post-hero mb-8 max-w-[640px] overflow-hidden rounded-lg border border-<?php echo esc_attr($side); ?>-border">
                        <?php the_post_thumbnail('large', [
                            'class'   => 'h-auto w-full object-cover',
                            'loading' => 'eager',
                            'alt'     => esc_attr(get_the_title()),
                        ]); ?>
                    </figure>
                <?php endif; ?>
                <header class="mb-8">
                    <div class="font-mono text-xs uppercase tracking-[0.2em] text-<?php echo esc_attr($side); ?>-muted"><?php echo esc_html(get_the_date()); ?></div>
                    <h1 class="mt-2 text-4xl font-bold tracking-tight md:text-5xl"><?php the_title(); ?></h1>
                </header>
                <div class="prose max-w-none text-<?php echo esc_attr($side); ?>-text">
                    <?php the_content(); ?>
                </div>
                <footer class="mt-12 border-t border-<?php echo esc_attr($side); ?>-border pt-6">
                    <a href="<?php echo esc_url($home_url . '#' . ($side === 'day' ? 'blog-day' : 'blog-night')); ?>" class="font-mono text-xs uppercase tracking-[0.2em] text-<?php echo esc_attr($side); ?>-accent">← <?php esc_html_e('Back to blog', 'funnie'); ?></a>
                </footer>
            </div>
        </article>
    <?php endwhile; ?>

</div>

<?php get_footer(); ?>
