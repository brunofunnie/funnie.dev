<?php if (!defined('ABSPATH')) exit; ?>
<div id="discord-modal" class="modal-shell" hidden>
    <div class="modal-card" role="dialog" aria-modal="true" aria-labelledby="discord-modal-title">
        <div id="discord-modal-title" class="font-mono text-xs uppercase tracking-[0.2em] text-night-muted">discord username</div>
        <div class="mt-3 text-2xl font-bold tracking-tight"><?php echo esc_html(funnie_settings('discord_handle', 'funnie')); ?></div>
        <button type="button" data-copy-text="<?php echo esc_attr(funnie_settings('discord_handle', 'funnie')); ?>" class="copy-btn mt-6" data-copied="false">Copy username</button>
        <div class="mt-6">
            <button type="button" data-discord-close class="font-mono text-xs uppercase tracking-[0.2em] text-night-accent">Close</button>
        </div>
    </div>
</div>
