<?php get_header(); ?>
<main class="mx-auto max-w-3xl px-6 py-24 text-center">
    <h1 class="text-7xl font-bold tracking-tight">404</h1>
    <p class="mt-4 text-day-muted"><?php esc_html_e('Page not found.', 'funnie'); ?></p>
    <p class="mt-8">
        <a href="<?php echo esc_url(home_url('/')); ?>" class="font-mono text-xs uppercase tracking-[0.2em] text-day-accent">← <?php esc_html_e('Back home', 'funnie'); ?></a>
    </p>
</main>
<?php get_footer(); ?>
