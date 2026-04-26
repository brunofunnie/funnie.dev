<?php get_header(); ?>
<?php
get_template_part('template-parts/modals');

$logo_top    = funnie_settings('hero_logo_top',    'funnie');
$logo_bottom = funnie_settings('hero_logo_bottom', 'dev');
?>

<?php get_template_part('template-parts/hero'); ?>

<main class="page-content">

    <!-- DAY block: sticky bar pins to the top while you're inside this block. -->
    <div class="content-block content-block-day" data-side="day">
        <header class="sticky-bar sticky-bar-day" aria-label="Day navigation">
            <div class="bar-scenery" aria-hidden="true">
                <svg class="bar-hills" viewBox="0 0 1200 90" preserveAspectRatio="none" fill="none">
                    <path d="M0,55 C160,30 320,55 500,40 C680,25 840,55 1000,38 C1120,28 1170,42 1200,40 L1200,90 L0,90 Z" fill="var(--hill-far)"/>
                    <path d="M0,68 C200,50 350,68 520,58 C680,48 820,65 980,55 C1100,48 1160,60 1200,58 L1200,90 L0,90 Z" fill="var(--hill-mid)"/>
                    <path d="M0,80 C160,72 320,80 500,76 C680,72 840,79 1000,75 C1120,72 1170,77 1200,76 L1200,90 L0,90 Z" fill="var(--hill-near)"/>
                </svg>
            </div>
            <div class="bar-weather-fx" aria-hidden="true">
                <div class="fx-clouds-extra"></div>
                <div class="fx-rain"></div>
                <div class="fx-snow"></div>
                <div class="fx-fog"></div>
                <div class="fx-storm-tint"></div>
                <div class="fx-lightning"></div>
            </div>
            <button type="button" class="bar-celestial bar-sun" data-scroll-to="hero" aria-label="Back to top">
                <span class="sun-mini" aria-hidden="true"></span>
            </button>
            <a class="bar-logo" href="#hero" data-scroll-to="hero">
                <span class="bar-logo-line"><?php echo esc_html($logo_top); ?></span>
                <span class="bar-logo-line"><?php echo esc_html($logo_bottom); ?></span>
            </a>
            <nav class="bar-nav" aria-label="Day sections">
                <button type="button" data-scroll-to="about"    class="nav-link day-nav">About</button>
                <button type="button" data-scroll-to="resume"   class="nav-link day-nav">Resume</button>
                <button type="button" data-scroll-to="blog-day" class="nav-link day-nav">Blog</button>
            </nav>
        </header>

        <?php
        get_template_part('template-parts/panel-about');
        get_template_part('template-parts/panel-resume');

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
        get_template_part('template-parts/panel-blog', null, [
            'panel_id'      => 'blog-day',
            'data_side'     => 'day',
            'category'      => funnie_settings('blog_day_category', 'professional'),
            'intro_default' => 'Notes from the day side. Web development, craft, and the occasional aside.',
            'intro_key'     => 'blog_day_intro',
            'placeholders'  => $day_placeholders,
        ]);
        ?>
    </div>

    <!-- NIGHT block. -->
    <div class="content-block content-block-night" data-side="night">
        <header class="sticky-bar sticky-bar-night" aria-label="Night navigation">
            <div class="bar-scenery" aria-hidden="true">
                <svg class="bar-city" viewBox="0 0 1200 90" preserveAspectRatio="none" fill="none">
                    <g fill="#040813">
                        <rect x="0"    y="62" width="80"  height="28"/>
                        <rect x="80"   y="50" width="60"  height="40"/>
                        <rect x="140"  y="68" width="40"  height="22"/>
                        <rect x="180"  y="42" width="80"  height="48"/>
                        <rect x="260"  y="58" width="50"  height="32"/>
                        <rect x="310"  y="48" width="70"  height="42"/>
                        <rect x="380"  y="64" width="40"  height="26"/>
                        <rect x="420"  y="38" width="90"  height="52"/>
                        <rect x="510"  y="56" width="60"  height="34"/>
                        <rect x="570"  y="46" width="80"  height="44"/>
                        <rect x="650"  y="62" width="50"  height="28"/>
                        <rect x="700"  y="40" width="100" height="50"/>
                        <rect x="800"  y="60" width="60"  height="30"/>
                        <rect x="860"  y="48" width="80"  height="42"/>
                        <rect x="940"  y="66" width="40"  height="24"/>
                        <rect x="980"  y="44" width="90"  height="46"/>
                        <rect x="1070" y="58" width="60"  height="32"/>
                        <rect x="1130" y="50" width="70"  height="40"/>
                    </g>
                    <g fill="#ffd66e" opacity="0.55">
                        <rect x="30"   y="76" width="2" height="2"/>
                        <rect x="100"  y="68" width="2" height="2"/>
                        <rect x="200"  y="60" width="2" height="2"/>
                        <rect x="330"  y="64" width="2" height="2"/>
                        <rect x="450"  y="56" width="2" height="2"/>
                        <rect x="600"  y="60" width="2" height="2"/>
                        <rect x="730"  y="56" width="2" height="2"/>
                        <rect x="900"  y="62" width="2" height="2"/>
                        <rect x="1010" y="58" width="2" height="2"/>
                        <rect x="1160" y="64" width="2" height="2"/>
                    </g>
                </svg>
            </div>
            <div class="bar-stars" aria-hidden="true">
                <span class="star" style="top:18%;left:8%;width:1.5px;height:1.5px;animation-delay:0s"></span>
                <span class="star" style="top:30%;left:22%;width:2px;height:2px;animation-delay:0.6s"></span>
                <span class="star" style="top:14%;left:38%;width:1px;height:1px;animation-delay:1.2s"></span>
                <span class="star" style="top:42%;left:54%;width:2px;height:2px;animation-delay:0.3s"></span>
                <span class="star" style="top:22%;left:68%;width:1.5px;height:1.5px;animation-delay:1.8s"></span>
                <span class="star" style="top:34%;left:82%;width:2.5px;height:2.5px;animation-delay:0.9s"></span>
                <span class="star" style="top:16%;left:92%;width:1px;height:1px;animation-delay:2.4s"></span>
            </div>
            <div class="shooting-stars" aria-hidden="true"></div>
            <div class="bar-weather-fx" aria-hidden="true">
                <div class="fx-clouds-extra"></div>
                <div class="fx-rain"></div>
                <div class="fx-snow"></div>
                <div class="fx-fog"></div>
                <div class="fx-storm-tint"></div>
                <div class="fx-lightning"></div>
            </div>
            <button type="button" class="bar-celestial bar-moon" data-scroll-to="hero" aria-label="Back to top">
                <span class="moon-mini" aria-hidden="true"><span class="moon-phase-shadow"></span></span>
            </button>
            <a class="bar-logo" href="#hero" data-scroll-to="hero">
                <span class="bar-logo-line"><?php echo esc_html($logo_top); ?></span>
                <span class="bar-logo-line"><?php echo esc_html($logo_bottom); ?></span>
            </a>
            <nav class="bar-nav" aria-label="Night sections">
                <button type="button" data-scroll-to="hardware"   class="nav-link night-nav">Hardware</button>
                <button type="button" data-scroll-to="blog-night" class="nav-link night-nav">Blog</button>
                <button type="button" data-scroll-to="socials"    class="nav-link night-nav">Socials</button>
            </nav>
        </header>

        <?php
        get_template_part('template-parts/panel-hardware');

        $night_placeholders = [
            'night-1' => [
                'date'    => '2026-02-14',
                'title'   => 'Hardware that actually matters in 2026',
                'excerpt' => 'An opinionated tour of the things that survived the year on my desk.',
                'body'    => '<p>Most of the hardware I bought in the last five years did nothing for the work. The chair did. The keyboard did. The monitor did, but mostly because the bad one was actively making things worse.</p><p>Everything else is a distraction with a bill of materials. I keep coming back to the same short list: a quiet keyboard, a screen that does not lie about color, and a chair that lets me forget I am sitting in one.</p>',
            ],
        ];
        get_template_part('template-parts/panel-blog', null, [
            'panel_id'      => 'blog-night',
            'data_side'     => 'night',
            'category'      => funnie_settings('blog_night_category', 'personal'),
            'intro_default' => 'Notes from the night side. Hardware, life, the occasional aside.',
            'intro_key'     => 'blog_night_intro',
            'placeholders'  => $night_placeholders,
        ]);

        get_template_part('template-parts/panel-socials');
        ?>
    </div>

</main>

<?php get_footer(); ?>
