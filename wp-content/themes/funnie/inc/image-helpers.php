<?php
if (!defined('ABSPATH')) exit;

/**
 * Normalize any ACF image value to an array with `url`, `alt`, `width`, `height`, `id`.
 * Accepts:
 *   - array (already full ACF array)
 *   - int   (attachment ID — resolved via wp_get_attachment_*)
 *   - null / '' / 0 (returns null)
 */
function funnie_image($val, string $size = 'large'): ?array {
    if (is_array($val) && !empty($val['url'])) {
        return $val + [
            'alt'    => $val['alt'] ?? '',
            'width'  => $val['width'] ?? null,
            'height' => $val['height'] ?? null,
            'id'     => $val['id'] ?? null,
        ];
    }
    if (is_numeric($val) && (int) $val > 0) {
        $id  = (int) $val;
        $src = wp_get_attachment_image_src($id, $size);
        if (!$src) return null;
        return [
            'id'     => $id,
            'url'    => $src[0],
            'width'  => $src[1],
            'height' => $src[2],
            'alt'    => (string) get_post_meta($id, '_wp_attachment_image_alt', true),
        ];
    }
    return null;
}
