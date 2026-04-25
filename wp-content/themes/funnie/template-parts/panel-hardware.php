<?php
if (!defined('ABSPATH')) exit;

$intro = funnie_settings('hardware_intro', "The kit. Quiet, dark, and generally over-spec'd for what it actually does.");

/**
 * Items, with their inline icon SVG. Editorial enough that we keep it as a
 * declarative array rather than fighting with HTML in ACF.
 */
$items = [
    ['kind' => 'machine',  'name' => 'MacBook Pro 16"',     'note' => 'M-series · 32GB · the daily.',       'svg' => '<rect x="3" y="4" width="18" height="12" rx="2"/><path d="M7 20h10M9 16v4M15 16v4"/>'],
    ['kind' => 'display',  'name' => '4K 27" Monitor',       'note' => 'Color-accurate panel for design review.', 'svg' => '<rect x="3" y="4" width="18" height="14" rx="2"/><path d="M9 21h6"/>'],
    ['kind' => 'keyboard', 'name' => 'Mechanical 75%',       'note' => 'Tactile switches · custom keycaps.', 'svg' => '<rect x="2" y="6" width="20" height="12" rx="2"/><path d="M6 10h.01M10 10h.01M14 10h.01M18 10h.01M6 14h12"/>'],
    ['kind' => 'mouse',    'name' => 'Wireless Ergo',        'note' => 'Light, quiet, two-week battery.',     'svg' => '<path d="M12 2a6 6 0 016 6v8a6 6 0 11-12 0V8a6 6 0 016-6z"/><path d="M12 6v6"/>'],
    ['kind' => 'audio',    'name' => 'Studio Headphones',    'note' => 'Closed-back · long sessions.',        'svg' => '<path d="M3 12c0-5 4-9 9-9s9 4 9 9v3a3 3 0 01-3 3h-2v-7h5"/><path d="M3 15v-3"/>'],
    ['kind' => 'camera',   'name' => '1080p Webcam',         'note' => 'Manual exposure · soft lighting.',    'svg' => '<rect x="3" y="6" width="18" height="12" rx="2"/><circle cx="12" cy="12" r="3"/>'],
    ['kind' => 'desk',     'name' => 'Standing Desk',        'note' => 'Memory presets · solid wood top.',    'svg' => '<path d="M3 10h18M5 10v10M19 10v10M5 20h14"/>'],
    ['kind' => 'chair',    'name' => 'Ergonomic Chair',      'note' => 'Adjustable everywhere · lumbar.',     'svg' => '<path d="M6 4h12v8H6zM6 12v6M18 12v6M5 22h14"/>'],
];
?>
<aside id="panel-hardware" class="panel panel-night" data-side="night" role="dialog" aria-modal="true" aria-labelledby="panel-hardware-title" hidden>
    <button type="button" class="panel-close" aria-label="Close Hardware panel">×</button>
    <h2 id="panel-hardware-title" class="panel-title">Hardware</h2>
    <div class="panel-body">
        <p class="mb-8 max-w-prose text-night-muted"><?php echo esc_html($intro); ?></p>
        <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
            <?php foreach ($items as $item):
                $allowed_svg = [
                    'rect'    => ['x' => [], 'y' => [], 'width' => [], 'height' => [], 'rx' => []],
                    'path'    => ['d' => []],
                    'circle'  => ['cx' => [], 'cy' => [], 'r' => []],
                    'polygon' => ['points' => []],
                    'line'    => ['x1' => [], 'y1' => [], 'x2' => [], 'y2' => []],
                ];
            ?>
                <article class="relative rounded-lg border border-night-border bg-night-surface p-5 transition hover:-translate-y-1 hover:shadow-[0_0_30px_rgba(167,139,250,0.25)]">
                    <svg class="absolute right-4 top-4 h-4 w-4 text-night-accent" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <?php echo wp_kses($item['svg'], $allowed_svg); ?>
                    </svg>
                    <div class="font-mono text-[0.65rem] uppercase tracking-[0.2em] text-night-muted"><?php echo esc_html($item['kind']); ?></div>
                    <div class="mt-2 text-base font-medium"><?php echo esc_html($item['name']); ?></div>
                    <div class="mt-1 text-xs text-night-muted"><?php echo esc_html($item['note']); ?></div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</aside>
