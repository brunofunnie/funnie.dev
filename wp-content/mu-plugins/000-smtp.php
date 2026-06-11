<?php
/**
 * Plugin Name: SMTP (Mailpit)
 * Description: Routes wp_mail through the Mailpit SMTP catcher in dev.
 */

if (!defined('ABSPATH')) exit;

add_filter('wp_mail_from',      function () { return 'wordpress@funnie.test'; });
add_filter('wp_mail_from_name', function () { return 'Funnie'; });

add_action('phpmailer_init', function ($mailer) {
    $mailer->isSMTP();
    $mailer->Host        = 'mailpit';
    $mailer->Port        = 1025;
    $mailer->SMTPAuth    = false;
    $mailer->SMTPAutoTLS = false;
    $mailer->SMTPSecure  = '';
});
