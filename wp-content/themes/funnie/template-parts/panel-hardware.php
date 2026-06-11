<?php
if (!defined('ABSPATH')) exit;

$intro = funnie_settings('hardware_intro', "The kit. Quiet, dark, and generally over-spec'd for what it actually does.");

$image_base = FUNNIE_THEME_URL . '/assets/images/hardware';

/**
 * Items grouped by section. Image filenames live under
 * /assets/images/hardware/. Editorial — declarative array beats fighting ACF.
 */
$sections = [
    [
        'label' => 'desk',
        'items' => [
            ['kind' => 'microphone', 'name' => 'HyperX Solocast',           'note' => 'USB cardioid · cantilever-mounted.',      'image' => 'microphone-hyperx-solocast.webp'],
            ['kind' => 'headphones', 'name' => 'HyperX Cloud III Wireless', 'note' => 'Closed-back · long sessions.',            'image' => 'headphone-hyperx-cloud-iii.webp'],
            ['kind' => 'webcam',     'name' => 'Fifine 1440p',              'note' => 'Manual exposure · soft key light.',       'image' => 'webcam-fifine-1440p.png'],
            ['kind' => 'keyboard',   'name' => 'Logitech G Pro X TKL',      'note' => 'Tenkeyless · tactile switches · black.',  'image' => 'keyboard-logitech-g-pro-x-tkl.webp'],
            ['kind' => 'mouse',      'name' => 'Logitech G Pro X Superlight 2', 'note' => 'Daily driver · feather-light wireless.', 'image' => 'mouse-logitech-superlight-2.webp'],
            ['kind' => 'mouse',      'name' => 'Redragon King Pro',         'note' => 'Spare · solid budget alternative.',       'image' => 'mouse-redragon-king-pro.avif'],
            ['kind' => 'desk',       'name' => 'Genio Standing Desk',       'note' => '160 × 80 cm · electric height presets.',  'image' => 'desk-genio-standing.png'],
        ],
    ],
    [
        'label' => 'tower',
        'items' => [
            ['kind' => 'motherboard', 'name' => 'Gigabyte B760M AORUS ELITE', 'note' => 'mATX · LGA 1700 · DDR5.',                'image' => 'motherboard-gigabyte-b760m-aorus-elite.jpg'],
            ['kind' => 'cpu',         'name' => 'Intel Core i5-14600K',       'note' => '14 cores · 20 threads · unlocked.',      'image' => 'processor-intel-i5-14600k.webp'],
            ['kind' => 'memory',      'name' => 'Kingston Fury Beast 32GB',   'note' => '2 × 16GB DDR5 · XMP profile.',           'image' => 'ram-kingston-fury-beast.webp'],
            ['kind' => 'gpu',         'name' => 'Galax GeForce RTX 5070',     'note' => '12GB · 1-Click OC · the lift.',          'image' => 'gpu-galax-rtx-5070.jpg'],
            ['kind' => 'storage',     'name' => 'Kingston Fury Renegade 2TB', 'note' => 'M.2 2280 · NVMe · the boot drive.',      'image' => 'ssd-kingston-fury-renegade-2tb.jpg'],
            ['kind' => 'cooling',     'name' => 'Rise Mode 240mm AIO',        'note' => 'ARGB · AMD/Intel · quiet under load.',   'image' => 'cooler-rise-mode-240mm.webp'],
            ['kind' => 'psu',         'name' => 'ASUS TUF Gaming 850W',       'note' => '80 Plus Gold · modular.',                'image' => 'psu-asus-tuf-gaming-850w.webp'],
            ['kind' => 'case',        'name' => 'Lian Li A3-mATX',            'note' => 'Compact mATX · clean cable runs.',       'image' => 'case-lian-li-a3.jpg'],
        ],
    ],
    [
        'label' => 'displays',
        'items' => [
            ['kind' => 'monitor', 'name' => 'ASUS TUF VG27AQL3A',  'note' => '27" QHD · 180Hz · the primary.',  'image' => 'monitor-asus-tuf-vg27aql3a.webp'],
            ['kind' => 'monitor', 'name' => 'Dell P2719H',          'note' => '27" FHD · IPS · vertical comms.', 'image' => 'monitor-dell-p2719h.webp'],
        ],
    ],
    [
        'label' => 'laptops',
        'items' => [
            ['kind' => 'laptop', 'name' => 'MacBook Pro 14"',  'note' => 'M3 Pro · 18GB · 512GB · the travel.', 'image' => 'laptop-macbook-m3-pro-14.jpg'],
            ['kind' => 'laptop', 'name' => 'MacBook Pro 16"',  'note' => 'M4 Pro · 24GB · 512GB · the work.',   'image' => 'laptop-macbook-m4-pro-16.jpeg'],
        ],
    ],
    [
        'label' => 'storage',
        'items' => [
            ['kind' => 'nas', 'name' => 'QNAP TS-433', 'note' => '4 × Seagate IronWolf Pro 8TB.', 'image' => 'nas-qnap-ts-433.webp'],
        ],
    ],
];
?>
<section id="hardware" class="panel panel-night" data-side="night" aria-labelledby="panel-hardware-title">
    <h2 id="panel-hardware-title" class="panel-title">Hardware</h2>
    <div class="panel-body">
        <p class="mb-10 max-w-prose text-night-muted"><?php echo esc_html($intro); ?></p>

        <?php foreach ($sections as $section): ?>
            <section class="mt-10 first:mt-0">
                <div class="mb-4 font-mono text-xs uppercase tracking-[0.2em] text-night-muted">// <?php echo esc_html($section['label']); ?></div>
                <div class="grid grid-cols-3 gap-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6">
                    <?php foreach ($section['items'] as $item):
                        $img_url = $image_base . '/' . $item['image'];
                    ?>
                        <button
                            type="button"
                            class="hardware-card group relative flex flex-col rounded-lg border border-night-border bg-night-surface p-2 text-left transition hover:-translate-y-1 hover:shadow-[0_0_30px_rgba(167,139,250,0.25)] focus:outline-none focus-visible:ring-2 focus-visible:ring-night-accent"
                            data-hardware-open
                            data-image="<?php echo esc_url($img_url); ?>"
                            data-kind="<?php echo esc_attr($item['kind']); ?>"
                            data-name="<?php echo esc_attr($item['name']); ?>"
                            data-note="<?php echo esc_attr($item['note']); ?>"
                            aria-label="<?php echo esc_attr($item['name']) . ' — ' . esc_attr($item['note']); ?>"
                        >
                            <div class="hardware-thumb mb-2 flex aspect-square items-center justify-center overflow-hidden rounded-md bg-[rgba(255,255,255,0.04)]">
                                <img src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($item['name']); ?>" class="h-full w-full object-contain transition duration-300 group-hover:scale-105" loading="lazy" />
                            </div>
                            <div class="font-mono text-[0.6rem] uppercase tracking-[0.2em] text-night-muted"><?php echo esc_html($item['kind']); ?></div>
                            <div class="mt-0.5 text-sm font-medium leading-tight"><?php echo esc_html($item['name']); ?></div>
                        </button>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endforeach; ?>
    </div>

    <div id="hardware-lightbox" class="hardware-lightbox" hidden role="dialog" aria-modal="true" aria-labelledby="hardware-lightbox-name">
        <div class="hardware-lightbox-backdrop" data-hardware-close></div>
        <figure class="hardware-lightbox-frame">
            <img class="hardware-lightbox-img" src="" alt="" />
            <figcaption class="hardware-lightbox-caption">
                <span class="hardware-lightbox-kind"></span>
                <span class="hardware-lightbox-name" id="hardware-lightbox-name"></span>
                <span class="hardware-lightbox-note"></span>
            </figcaption>
        </figure>
        <button type="button" class="hardware-lightbox-close" data-hardware-close aria-label="Close">×</button>
    </div>
</section>
