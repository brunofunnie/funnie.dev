<?php
/**
 * Plugin Name: Dev Uploads
 * Description: Allow SVG + extra MIME types in dev so layout assets import cleanly.
 */

add_filter('upload_mimes', function (array $mimes): array {
    $mimes['svg']  = 'image/svg+xml';
    $mimes['svgz'] = 'image/svg+xml';
    $mimes['webp'] = 'image/webp';
    return $mimes;
});

add_filter('wp_check_filetype_and_ext', function ($data, $file, $filename, $mimes) {
    if (substr($filename, -4) === '.svg') {
        return ['ext' => 'svg', 'type' => 'image/svg+xml', 'proper_filename' => $filename];
    }
    if (substr($filename, -5) === '.svgz') {
        return ['ext' => 'svgz', 'type' => 'image/svg+xml', 'proper_filename' => $filename];
    }
    return $data;
}, 10, 4);
