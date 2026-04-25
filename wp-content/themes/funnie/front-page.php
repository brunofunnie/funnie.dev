<?php get_header(); ?>
<?php
get_template_part('template-parts/debug-box');
get_template_part('template-parts/modals');

// Panel overlays (full-viewport, opened by nav).
?>
<div id="panel-backdrop" class="pointer-events-none fixed inset-0 z-30 bg-black/30 opacity-0 transition-opacity duration-500" aria-hidden="true"></div>
<div id="panels" class="pointer-events-none fixed inset-0 z-40">
    <?php
    get_template_part('template-parts/panel-about');
    get_template_part('template-parts/panel-resume');
    get_template_part('template-parts/panel-hardware');

    // Two blog panels — one per side, filtered by category.
    $day_placeholders = [
        'day-1' => [
            'date'    => '2026-03-22',
            'title'   => 'Notes on shipping fast with vanilla JS',
            'excerpt' => 'No build step, no framework, no excuses — what holds up and what does not.',
            'body'    => '<p>Most of the slowness I have lived with on the web came from layers I added before I had a problem. A bundler. A framework. A toolchain that could not start in under a minute. For a small site, none of that was earning its keep.</p><p>Vanilla JS plus a CDN-loaded utility CSS framework is — for a site of this size — quietly correct. The cost of the abstraction shows up later, and you can pay it then.</p>',
        ],
        'day-2' => [
            'date'    => '2026-04-10',
            'title'   => 'Why I split my site into day and night',
            'excerpt' => 'A short defense of metaphor on the open web — and why a hover should mean something.',
            'body'    => '<p>For a long time my homepage was a single column. Useful, neutral, and exactly as personal as a letterhead. The split — day on the left, night on the right — was a small bet that a personal site can carry a metaphor without collapsing under it.</p><p>I do not think every site needs a metaphor. I think the ones that have one should commit to it.</p>',
        ],
    ];
    $night_placeholders = [
        'night-1' => [
            'date'    => '2026-02-14',
            'title'   => 'Hardware that actually matters in 2026',
            'excerpt' => 'An opinionated tour of the things that survived the year on my desk.',
            'body'    => '<p>Most of the hardware I bought in the last five years did nothing for the work. The chair did. The keyboard did. The monitor did, but mostly because the bad one was actively making things worse.</p><p>Everything else is a distraction with a bill of materials. I keep coming back to the same short list: a quiet keyboard, a screen that does not lie about color, and a chair that lets me forget I am sitting in one.</p>',
        ],
    ];

    get_template_part('template-parts/panel-blog', null, [
        'panel_id'      => 'panel-blog-day',
        'data_side'     => 'day',
        'category'      => funnie_settings('blog_day_category', 'professional'),
        'intro_default' => 'Notes from the day side. Web development, craft, and the occasional aside.',
        'intro_key'     => 'blog_day_intro',
        'placeholders'  => $day_placeholders,
    ]);
    get_template_part('template-parts/panel-blog', null, [
        'panel_id'      => 'panel-blog-night',
        'data_side'     => 'night',
        'category'      => funnie_settings('blog_night_category', 'personal'),
        'intro_default' => 'Notes from the night side. Hardware, life, the occasional aside.',
        'intro_key'     => 'blog_night_intro',
        'placeholders'  => $night_placeholders,
    ]);

    get_template_part('template-parts/panel-socials');
    ?>
</div>

<?php get_template_part('template-parts/hero'); ?>

<?php get_footer(); ?>
