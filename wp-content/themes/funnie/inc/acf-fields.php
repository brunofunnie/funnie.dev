<?php
if (!defined('ABSPATH')) exit;
if (!function_exists('acf_add_local_field_group')) return;

$settings_location = [[
    ['param' => 'post_type', 'operator' => '==', 'value' => 'site_settings'],
]];

$rows_help = 'One row per item. Columns separated by "|". Blank lines and lines starting with # are skipped.';

/* Hero — logo, day/night labels and taglines. */
acf_add_local_field_group([
    'key'      => 'group_funnie_hero',
    'title'    => 'Hero',
    'location' => $settings_location,
    'fields'   => [
        ['key' => 'field_hero_logo_top',    'name' => 'hero_logo_top',    'label' => 'Logo — top line',    'type' => 'text', 'default_value' => 'funnie'],
        ['key' => 'field_hero_logo_bottom', 'name' => 'hero_logo_bottom', 'label' => 'Logo — bottom line', 'type' => 'text', 'default_value' => 'dev'],
        ['key' => 'field_hero_day_label',   'name' => 'hero_day_label',   'label' => 'Day big heading',    'type' => 'text', 'default_value' => 'DAY'],
        ['key' => 'field_hero_night_label',     'name' => 'hero_night_label',     'label' => 'Night big heading',      'type' => 'text', 'default_value' => 'NIGHT'],
        ['key' => 'field_hero_day_tagline',     'name' => 'hero_day_tagline',     'label' => 'Day footer tagline',     'type' => 'text', 'default_value' => '// professional'],
        ['key' => 'field_hero_night_tagline',   'name' => 'hero_night_tagline',   'label' => 'Night footer tagline',   'type' => 'text', 'default_value' => '// personal'],
    ],
]);

/* About panel. */
acf_add_local_field_group([
    'key'      => 'group_funnie_about',
    'title'    => 'About panel',
    'location' => $settings_location,
    'fields'   => [
        ['key' => 'field_about_avatar',     'name' => 'about_avatar',     'label' => 'Avatar (optional — falls back to bundled SVG)', 'type' => 'image', 'return_format' => 'array'],
        ['key' => 'field_about_name',       'name' => 'about_name',       'label' => 'Display name', 'type' => 'text', 'default_value' => 'Bruno Oliveira'],
        ['key' => 'field_about_alias',      'name' => 'about_alias',      'label' => 'Alias caption', 'type' => 'text', 'default_value' => 'aka Funnie'],
        ['key' => 'field_about_paragraphs', 'name' => 'about_paragraphs', 'label' => 'Bio paragraphs', 'type' => 'textarea', 'rows' => 8, 'instructions' => 'Separate paragraphs with a blank line.'],
        ['key' => 'field_about_stack',      'name' => 'about_stack',      'label' => 'Stack chips', 'type' => 'textarea', 'rows' => 8, 'instructions' => $rows_help . ' Columns: kind | name (e.g. "language | Rust").'],
    ],
]);

/* Resume panel. */
acf_add_local_field_group([
    'key'      => 'group_funnie_resume',
    'title'    => 'Resume panel',
    'location' => $settings_location,
    'fields'   => [
        ['key' => 'field_resume_intro',     'name' => 'resume_intro',     'label' => 'Intro line', 'type' => 'text'],
        ['key' => 'field_resume_pdf_url',   'name' => 'resume_pdf_url',   'label' => 'PDF download URL', 'type' => 'url'],
        ['key' => 'field_resume_edu_period','name' => 'resume_edu_period','label' => 'Education — period', 'type' => 'text', 'default_value' => '2014 — 2018'],
        ['key' => 'field_resume_edu_title', 'name' => 'resume_edu_title', 'label' => 'Education — title',  'type' => 'text', 'default_value' => 'B.Sc. Computer Science'],
        ['key' => 'field_resume_edu_school','name' => 'resume_edu_school','label' => 'Education — school', 'type' => 'text', 'default_value' => 'Placeholder University'],
    ],
]);

/* Hardware panel. */
acf_add_local_field_group([
    'key'      => 'group_funnie_hardware',
    'title'    => 'Hardware panel',
    'location' => $settings_location,
    'fields'   => [
        ['key' => 'field_hardware_intro', 'name' => 'hardware_intro', 'label' => 'Intro line', 'type' => 'text'],
    ],
]);

/* Blog panels — day & night each pull from a different category. */
acf_add_local_field_group([
    'key'      => 'group_funnie_blog',
    'title'    => 'Blog panels',
    'location' => $settings_location,
    'fields'   => [
        ['key' => 'field_blog_day_intro',     'name' => 'blog_day_intro',     'label' => 'Day intro line',         'type' => 'text'],
        ['key' => 'field_blog_day_category',  'name' => 'blog_day_category',  'label' => 'Day category slug',      'type' => 'text', 'default_value' => 'professional', 'instructions' => 'WP category slug to filter the day-side blog by.'],
        ['key' => 'field_blog_night_intro',   'name' => 'blog_night_intro',   'label' => 'Night intro line',       'type' => 'text'],
        ['key' => 'field_blog_night_category','name' => 'blog_night_category','label' => 'Night category slug',    'type' => 'text', 'default_value' => 'personal',     'instructions' => 'WP category slug to filter the night-side blog by.'],
    ],
]);

/* Socials panel + Discord modal. */
acf_add_local_field_group([
    'key'      => 'group_funnie_socials',
    'title'    => 'Socials panel',
    'location' => $settings_location,
    'fields'   => [
        ['key' => 'field_socials_intro',            'name' => 'socials_intro',            'label' => 'Intro line', 'type' => 'text'],
        ['key' => 'field_socials_instagram_url',    'name' => 'socials_instagram_url',    'label' => 'Instagram URL', 'type' => 'url'],
        ['key' => 'field_socials_instagram_handle', 'name' => 'socials_instagram_handle', 'label' => 'Instagram handle', 'type' => 'text'],
        ['key' => 'field_socials_github_url',       'name' => 'socials_github_url',       'label' => 'GitHub URL', 'type' => 'url'],
        ['key' => 'field_socials_github_handle',    'name' => 'socials_github_handle',    'label' => 'GitHub handle', 'type' => 'text'],
        ['key' => 'field_socials_discord_label',    'name' => 'socials_discord_label',    'label' => 'Discord card subtitle', 'type' => 'text'],
        ['key' => 'field_discord_handle',           'name' => 'discord_handle',           'label' => 'Discord username (used in modal + copy)', 'type' => 'text'],
        ['key' => 'field_contact_email',            'name' => 'contact_email',            'label' => 'Contact email', 'type' => 'email'],
    ],
]);
