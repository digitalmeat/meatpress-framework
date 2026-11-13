<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Meatpress
|--------------------------------------------------------------------------
|
| MEAT Code conventions shared by every Meatpress site. Each behavior can be
| turned off per project: publish this file with
|
|     php artisan vendor:publish --tag=meatpress-config
|
*/

return [

    /*
    |--------------------------------------------------------------------------
    | Search engine visibility
    |--------------------------------------------------------------------------
    |
    | Add "noindex, nofollow" on every environment but production (APP_ENV),
    | whatever the "Search engine visibility" setting of WordPress says.
    |
    */
    'noindex_outside_production' => true,

    /*
    |--------------------------------------------------------------------------
    | Searchable custom fields
    |--------------------------------------------------------------------------
    |
    | Copy the text stored in custom fields (ACF) into post_content on save,
    | so the native WordPress search finds pages built with custom fields.
    | Meta keys matching one of the SQL LIKE patterns are indexed.
    |
    */
    'searchable_custom_fields' => [
        'enabled' => true,
        'meta_key_patterns' => ['%text%', '%title%', '%descript%'],
        'ignored_post_types' => ['acf-field-group', 'acf-field', 'revision', 'nav_menu_item'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Clean <head>
    |--------------------------------------------------------------------------
    |
    | Remove front-end assets WordPress prints by default that a theme styled
    | with its own CSS does not use. Turn "block_styles" off when the site
    | renders Gutenberg blocks.
    |
    */
    'clean_head' => [
        'block_styles' => true,
        'global_styles' => true,
        'emojis' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | ACF local JSON
    |--------------------------------------------------------------------------
    |
    | Save ACF field groups as JSON in the active theme ("acf-json" directory
    | by default) so they are versioned and synced between environments.
    |
    */
    'acf_local_json' => [
        'enabled' => true,
        'directory' => 'acf-json',
    ],

    /*
    |--------------------------------------------------------------------------
    | Optional plugins
    |--------------------------------------------------------------------------
    |
    | Plugins installed by the base but left inactive by "plugins:activate"
    | unless they are named explicitly. Tracking is one of GTM or GA4 per
    | site: activating both would count every visit twice.
    |
    */
    'optional_plugins' => [
        'duracelltomi-google-tag-manager',
        'ga-google-analytics',
    ],

];
