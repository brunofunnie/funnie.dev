<?php get_header(); ?>
<main class="mx-auto max-w-3xl px-6 py-16">
    <header class="mb-10">
        <div class="font-mono text-xs uppercase tracking-[0.2em] text-day-muted">Blog</div>
        <h1 class="mt-2 text-4xl font-bold tracking-tight">
            <?php echo esc_html(is_home() ? (single_post_title('', false) ?: __('Blog', 'funnie')) : __('Blog', 'funnie')); ?>
        </h1>
    </header>

    <?php if (have_posts()): ?>
        <ul class="space-y-6">
            <?php while (have_posts()): the_post(); ?>
                <li>
                    <a href="<?php the_permalink(); ?>" class="block rounded-lg border border-day-border bg-day-surface p-5 transition hover:-translate-y-1 hover:shadow-md">
                        <div class="font-mono text-xs uppercase tracking-[0.2em] text-day-muted"><?php echo esc_html(get_the_date()); ?></div>
                        <div class="mt-1 text-lg font-bold"><?php the_title(); ?></div>
                        <div class="mt-2 text-sm text-day-muted"><?php the_excerpt(); ?></div>
                    </a>
                </li>
            <?php endwhile; ?>
        </ul>
        <nav class="mt-10" aria-label="<?php esc_attr_e('Pagination', 'funnie'); ?>">
            <?php the_posts_pagination(['prev_text' => '‹', 'next_text' => '›', 'mid_size' => 1]); ?>
        </nav>
    <?php else: ?>
        <p class="text-day-muted"><?php esc_html_e('No posts yet.', 'funnie'); ?></p>
    <?php endif; ?>
</main>
<?php get_footer(); ?>
