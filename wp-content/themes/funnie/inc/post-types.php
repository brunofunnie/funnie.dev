<?php
if (!defined('ABSPATH')) exit;

add_action('init', function () {

    register_post_type('site_settings', [
        'label'        => __('Site Settings', 'funnie'),
        'labels'       => [
            'name'          => __('Site Settings', 'funnie'),
            'singular_name' => __('Site Setting',  'funnie'),
        ],
        'public'       => false,
        'show_ui'      => true,
        'show_in_rest' => false,
        'menu_icon'    => 'dashicons-admin-generic',
        'menu_position'=> 80,
        'supports'     => ['title'],
        'capabilities' => [
            'create_posts' => 'do_not_allow',
        ],
        'map_meta_cap' => true,
    ]);
});
