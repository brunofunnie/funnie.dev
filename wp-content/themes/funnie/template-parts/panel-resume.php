<?php
if (!defined('ABSPATH')) exit;

$intro    = funnie_settings('resume_intro', 'A focused look at the work and the path that led here.');
$pdf_url  = funnie_settings('resume_pdf_url', '#');

$default_jobs = [
    [
        'period'  => '2023 — Present',
        'title'   => 'Senior Web Developer · FunnieTech',
        'bullets' => [
            'Lead architect on the company web platform; set frontend direction and review most pull requests.',
            'Drove a rebuild of the customer-facing app, cutting page load times by an order of magnitude.',
            'Mentor a small team of engineers across product, infrastructure, and design adjacency.',
        ],
        'tags'    => ['TypeScript', 'React', 'Node.js', 'PostgreSQL', 'AWS'],
    ],
    [
        'period'  => '2020 — 2023',
        'title'   => 'Frontend Engineer · Placeholder Co.',
        'bullets' => [
            'Owned the design-system rollout across three product surfaces.',
            'Shipped accessibility improvements that lifted Lighthouse scores from the low 70s to consistent 95+.',
            'Worked closely with designers to translate Figma into composable, typed components.',
        ],
        'tags'    => ['React', 'TypeScript', 'Storybook', 'Tailwind CSS'],
    ],
    [
        'period'  => '2018 — 2020',
        'title'   => 'Junior Developer · Acme Inc.',
        'bullets' => [
            'First professional role. Built and maintained internal tools across the stack.',
            'Took ownership of two long-standing bug categories and quietly retired both.',
        ],
        'tags'    => ['JavaScript', 'Vue', 'Express'],
    ],
];

$default_education = [
    'period' => '2014 — 2018',
    'title'  => 'B.Sc. Computer Science',
    'school' => 'Placeholder University',
];
$edu_period = funnie_settings('resume_edu_period', $default_education['period']);
$edu_title  = funnie_settings('resume_edu_title',  $default_education['title']);
$edu_school = funnie_settings('resume_edu_school', $default_education['school']);
?>
<section id="resume" class="panel panel-day" data-side="day" aria-labelledby="panel-resume-title">
    <h2 id="panel-resume-title" class="panel-title">Resume</h2>
    <div class="panel-body">
        <div class="mb-8 flex items-start justify-between gap-6">
            <p class="max-w-prose text-day-muted"><?php echo esc_html($intro); ?></p>
            <a href="<?php echo esc_url($pdf_url); ?>" class="rounded-lg border border-day-border bg-day-surface px-4 py-2 font-mono text-xs uppercase tracking-[0.2em] text-day-text transition hover:border-day-accent">Download PDF</a>
        </div>

        <section>
            <div class="font-mono text-xs uppercase tracking-[0.2em] text-day-muted">// experience</div>
            <ol class="timeline mt-6">
                <?php foreach ($default_jobs as $job): ?>
                    <li class="timeline-item">
                        <div class="font-mono text-xs uppercase tracking-[0.2em] text-day-muted"><?php echo esc_html($job['period']); ?></div>
                        <div class="mt-1 text-lg font-bold"><?php echo esc_html($job['title']); ?></div>
                        <ul class="mt-2 list-disc space-y-1 pl-5 text-day-text">
                            <?php foreach ($job['bullets'] as $b): ?>
                                <li><?php echo esc_html($b); ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <div class="mt-2">
                            <?php foreach ($job['tags'] as $t): ?>
                                <span class="tag"><?php echo esc_html($t); ?></span>
                            <?php endforeach; ?>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ol>
        </section>

        <section class="mt-12">
            <div class="font-mono text-xs uppercase tracking-[0.2em] text-day-muted">// education</div>
            <div class="mt-4 rounded-lg border border-day-border bg-day-surface p-5">
                <div class="font-mono text-xs uppercase tracking-[0.2em] text-day-muted"><?php echo esc_html($edu_period); ?></div>
                <div class="mt-1 text-lg font-bold"><?php echo esc_html($edu_title); ?></div>
                <div class="text-day-muted"><?php echo esc_html($edu_school); ?></div>
            </div>
        </section>
    </div>
</section>
