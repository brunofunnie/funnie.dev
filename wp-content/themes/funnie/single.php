<?php get_header(); ?>
<main class="mx-auto max-w-3xl px-6 py-16">
    <?php while (have_posts()): the_post(); ?>
        <article>
            <header class="mb-8">
                <div class="font-mono text-xs uppercase tracking-[0.2em] text-day-muted"><?php echo esc_html(get_the_date()); ?></div>
                <h1 class="mt-2 text-4xl font-bold tracking-tight"><?php the_title(); ?></h1>
            </header>
            <div class="prose prose-neutral max-w-none text-day-text">
                <?php the_content(); ?>
            </div>
            <footer class="mt-12">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="font-mono text-xs uppercase tracking-[0.2em] text-day-accent">← <?php esc_html_e('Back home', 'funnie'); ?></a>
            </footer>
        </article>
    <?php endwhile; ?>
</main>
<?php get_footer(); ?>
