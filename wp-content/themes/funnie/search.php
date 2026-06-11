<?php get_header(); ?>
<main class="mx-auto max-w-3xl px-6 py-16">
    <header class="mb-10">
        <h1 class="text-2xl font-bold tracking-tight">
            <?php printf(esc_html__('Results for "%s"', 'funnie'), esc_html(get_search_query())); ?>
        </h1>
    </header>
    <?php if (have_posts()): ?>
        <ul class="space-y-6">
            <?php while (have_posts()): the_post(); ?>
                <li>
                    <a href="<?php the_permalink(); ?>" class="block rounded-lg border border-day-border bg-day-surface p-5 transition hover:-translate-y-1 hover:shadow-md">
                        <h2 class="text-lg font-bold"><?php the_title(); ?></h2>
                        <div class="mt-2 text-sm text-day-muted"><?php the_excerpt(); ?></div>
                    </a>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <p class="text-day-muted"><?php esc_html_e('Nothing found.', 'funnie'); ?></p>
    <?php endif; ?>
</main>
<?php get_footer(); ?>
