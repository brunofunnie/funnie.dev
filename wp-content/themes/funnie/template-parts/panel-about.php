<?php
if (!defined('ABSPATH')) exit;

$avatar_field = funnie_image(funnie_settings('about_avatar'));
$avatar_url   = $avatar_field['url'] ?? (FUNNIE_THEME_URL . '/assets/avatar-bo.svg');
$avatar_alt   = $avatar_field['alt'] ?? 'Bruno Oliveira';

$display_name = funnie_settings('about_name',  'Bruno Oliveira');
$alias        = funnie_settings('about_alias', 'aka Funnie');

$default_paragraphs = [
    'I build for the web. The work spans interfaces that respond to a single hover, systems that survive a million of them, and the small interactions in between that make the rest feel alive.',
    'I have been writing code professionally for the better part of a decade across startups and product teams. I care about clarity, type-safety where it earns its keep, and shipping things that actually feel good to use.',
    'Right now I am most interested in the seams between design and engineering — building tools that respect the craft of both, and writing software that is honest about how it works.',
];
$paragraphs_text = funnie_settings('about_paragraphs');
$paragraphs = $paragraphs_text
    ? array_filter(array_map('trim', preg_split('/\r?\n\r?\n/', (string) $paragraphs_text)))
    : $default_paragraphs;

$default_stack = [
    ['kind' => 'language',  'name' => 'JavaScript'],
    ['kind' => 'language',  'name' => 'TypeScript'],
    ['kind' => 'framework', 'name' => 'React'],
    ['kind' => 'runtime',   'name' => 'Node.js'],
    ['kind' => 'styling',   'name' => 'Tailwind CSS'],
    ['kind' => 'language',  'name' => 'Rust'],
    ['kind' => 'database',  'name' => 'PostgreSQL'],
    ['kind' => 'cloud',     'name' => 'AWS'],
];
$stack = funnie_parse_rows(funnie_settings('about_stack'), ['kind', 'name']);
if (!$stack) $stack = $default_stack;
?>
<aside id="panel-about" class="panel panel-day" data-side="day" role="dialog" aria-modal="true" aria-labelledby="panel-about-title" hidden>
    <button type="button" class="panel-close" aria-label="Close About panel">×</button>
    <h2 id="panel-about-title" class="panel-title">About</h2>
    <div class="panel-body">
        <div class="grid gap-10 md:grid-cols-[240px,1fr] md:items-start">
            <div class="flex flex-col items-center md:items-start">
                <img src="<?php echo esc_url($avatar_url); ?>" alt="<?php echo esc_attr($avatar_alt); ?>" class="h-60 w-60 rounded-full" />
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
</aside>
