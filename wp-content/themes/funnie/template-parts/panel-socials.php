<?php
if (!defined('ABSPATH')) exit;

$intro          = funnie_settings('socials_intro', 'No contact form. Pick the room you actually like talking in.');
$ig_url         = funnie_settings('socials_instagram_url', 'https://instagram.com/brunofunnie');
$ig_handle      = funnie_settings('socials_instagram_handle', '@brunofunnie');
$gh_url         = funnie_settings('socials_github_url', 'https://github.com/brunofunnie');
$gh_handle      = funnie_settings('socials_github_handle', '@brunofunnie');
$discord_url    = funnie_settings('socials_discord_url', 'https://discord.gg/buteco-dos-devs');
$discord_label  = funnie_settings('socials_discord_label', 'Buteco dos Devs · server invite');
?>
<section id="socials" class="panel panel-night" data-side="night" aria-labelledby="panel-socials-title">
    <h2 id="panel-socials-title" class="panel-title">Socials</h2>
    <div class="panel-body">
        <p class="mb-10 max-w-prose text-night-muted"><?php echo esc_html($intro); ?></p>

        <div class="grid gap-4 md:grid-cols-3">
            <a href="<?php echo esc_url($ig_url); ?>" target="_blank" rel="noreferrer noopener" class="social-card social-card--instagram group block rounded-lg border border-night-border bg-night-surface p-6 transition hover:-translate-y-1">
                <svg class="social-card__icon h-8 w-8 text-night-accent" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor"/></svg>
                <div class="social-card__title mt-4 text-lg font-bold">Instagram</div>
                <div class="social-card__handle font-mono text-xs uppercase tracking-[0.2em] text-night-muted"><?php echo esc_html($ig_handle); ?></div>
            </a>

            <a href="<?php echo esc_url($gh_url); ?>" target="_blank" rel="noreferrer noopener" class="social-card social-card--github group block rounded-lg border border-night-border bg-night-surface p-6 transition hover:-translate-y-1">
                <svg class="social-card__icon h-8 w-8 text-night-accent" viewBox="0 0 24 24" fill="currentColor"><path d="M12 .5C5.65.5.5 5.65.5 12c0 5.08 3.29 9.39 7.86 10.91.58.11.79-.25.79-.55v-2.13c-3.2.7-3.87-1.36-3.87-1.36-.52-1.32-1.27-1.67-1.27-1.67-1.04-.71.08-.7.08-.7 1.15.08 1.76 1.18 1.76 1.18 1.02 1.75 2.69 1.25 3.34.96.1-.74.4-1.25.72-1.54-2.55-.29-5.24-1.27-5.24-5.66 0-1.25.45-2.27 1.18-3.07-.12-.29-.51-1.46.11-3.04 0 0 .96-.31 3.15 1.17.92-.26 1.9-.39 2.88-.39s1.96.13 2.88.39c2.19-1.48 3.15-1.17 3.15-1.17.62 1.58.23 2.75.11 3.04.74.8 1.18 1.82 1.18 3.07 0 4.4-2.69 5.36-5.25 5.65.41.36.78 1.06.78 2.14v3.18c0 .31.21.66.8.55C20.21 21.39 23.5 17.08 23.5 12 23.5 5.65 18.35.5 12 .5z"/></svg>
                <div class="social-card__title mt-4 text-lg font-bold">GitHub</div>
                <div class="social-card__handle font-mono text-xs uppercase tracking-[0.2em] text-night-muted"><?php echo esc_html($gh_handle); ?></div>
            </a>

            <a href="<?php echo esc_url($discord_url); ?>" target="_blank" rel="noreferrer noopener" class="social-card social-card--discord group block rounded-lg border border-night-border bg-night-surface p-6 transition hover:-translate-y-1">
                <svg class="social-card__icon h-8 w-8 text-night-accent" viewBox="0 0 24 24" fill="currentColor"><path d="M20.32 4.37A19.79 19.79 0 0 0 16 3l-.2.4a17.18 17.18 0 0 0-7.6 0L8 3a19.79 19.79 0 0 0-4.32 1.37C1.4 9.55.84 14.6 1.12 19.6a19.95 19.95 0 0 0 6 3 14.6 14.6 0 0 0 1.27-2.06 12.86 12.86 0 0 1-2-1 .25.25 0 0 1 0-.4 14.06 14.06 0 0 0 .42-.34 14.27 14.27 0 0 0 12.36 0c.14.12.28.23.42.34a.25.25 0 0 1 0 .4 12.86 12.86 0 0 1-2 1 14.6 14.6 0 0 0 1.27 2.06 19.95 19.95 0 0 0 6-3c.36-5.55-.6-10.55-3.55-15.23zM9.55 15.5c-1.18 0-2.15-1.07-2.15-2.39s.95-2.39 2.15-2.39 2.16 1.08 2.15 2.39c0 1.32-.96 2.39-2.15 2.39zm4.9 0c-1.18 0-2.15-1.07-2.15-2.39s.95-2.39 2.15-2.39 2.16 1.08 2.15 2.39c0 1.32-.95 2.39-2.15 2.39z"/></svg>
                <div class="social-card__title mt-4 text-lg font-bold">Discord</div>
                <div class="social-card__handle font-mono text-xs uppercase tracking-[0.2em] text-night-muted"><?php echo esc_html($discord_label); ?></div>
            </a>
        </div>
    </div>
</section>
