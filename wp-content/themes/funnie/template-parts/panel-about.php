<?php
if (!defined('ABSPATH')) exit;

$avatar_field = funnie_image(funnie_settings('about_avatar'));
$avatar_url   = $avatar_field['url'] ?? (FUNNIE_THEME_URL . '/assets/images/funnie-photo.png');
$avatar_alt   = $avatar_field['alt'] ?? 'Bruno Oliveira';

$display_name = funnie_settings('about_name',  'Bruno Oliveira');
$alias        = funnie_settings('about_alias', 'aka Funnie');

$default_paragraphs = [
    'Senior Software Engineer with 10+ years building scalable web applications and REST APIs in PHP and Node.js — for companies like Jabil, Coca-Cola, and Alcatel Mobile, plus a long tail of smaller teams that taught me what actually breaks in production.',
    'Comfortable across the stack: Laravel, Symfony, and Express on the back end; Vue, React, and React Native on the front. Microservices when the system warrants it, Docker when the team does, and AWS when the bill clears.',
    'Earlier in my career I worked as an information security analyst — penetration testing, social engineering, ISO/IEC 27001/27015. That lens has stuck. Right now I am most interested in AI-assisted development: spec-driven workflows, prompt harnesses, and context engineering for code generation that actually ships.',
    'Based in Sorocaba, Brazil. I write Portuguese natively, English bilingually, and Spanish well enough to argue.',
];
$paragraphs_text = funnie_settings('about_paragraphs');
$paragraphs = $paragraphs_text
    ? array_filter(array_map('trim', preg_split('/\r?\n\r?\n/', (string) $paragraphs_text)))
    : $default_paragraphs;

$default_stack = [
    ['kind' => 'language',   'name' => 'PHP'],
    ['kind' => 'language',   'name' => 'JavaScript'],
    ['kind' => 'framework',  'name' => 'Laravel'],
    ['kind' => 'framework',  'name' => 'Symfony'],
    ['kind' => 'runtime',    'name' => 'Node.js'],
    ['kind' => 'framework',  'name' => 'Vue.js'],
    ['kind' => 'framework',  'name' => 'React'],
    ['kind' => 'mobile',     'name' => 'React Native'],
    ['kind' => 'database',   'name' => 'MySQL'],
    ['kind' => 'database',   'name' => 'PostgreSQL'],
    ['kind' => 'database',   'name' => 'MongoDB'],
    ['kind' => 'cache',      'name' => 'Redis'],
    ['kind' => 'cloud',      'name' => 'AWS'],
    ['kind' => 'devops',     'name' => 'Docker'],
    ['kind' => 'security',   'name' => 'PenTest'],
    ['kind' => 'practice',   'name' => 'AI-assisted dev'],
];
$stack = funnie_parse_rows(funnie_settings('about_stack'), ['kind', 'name']);
if (!$stack) $stack = $default_stack;
?>
<section id="about" class="panel panel-day" data-side="day" aria-labelledby="panel-about-title">
    <h2 id="panel-about-title" class="panel-title">About</h2>
    <div class="panel-body">
        <div class="grid gap-10 md:grid-cols-[240px,1fr] md:items-start">
            <div class="flex flex-col items-center md:items-start">
                <img src="<?php echo esc_url($avatar_url); ?>" alt="<?php echo esc_attr($avatar_alt); ?>" class="h-60 w-60 rounded-full object-cover" />
                <div class="mt-6 text-center md:text-left">
                    <div class="text-2xl font-bold tracking-tight"><?php echo esc_html($display_name); ?></div>
                    <div class="font-mono text-xs uppercase tracking-[0.2em] text-day-muted"><?php echo esc_html($alias); ?></div>
                </div>
            </div>
            <div class="space-y-4 text-base leading-relaxed text-day-text">
                <?php foreach ($paragraphs as $p): ?>
                    <p><?php echo esc_html($p); ?></p>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="mt-12">
            <div class="font-mono text-xs uppercase tracking-[0.2em] text-day-muted">// stack</div>
            <div class="mt-4 grid grid-cols-2 gap-3 md:grid-cols-4">
                <?php foreach ($stack as $item): ?>
                    <div class="rounded-lg border border-day-border bg-day-surface p-4">
                        <div class="font-mono text-[0.65rem] uppercase tracking-[0.2em] text-day-muted"><?php echo esc_html($item['kind']); ?></div>
                        <div class="mt-1 text-sm font-medium"><?php echo esc_html($item['name']); ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
