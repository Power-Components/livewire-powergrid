<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Theme
    |--------------------------------------------------------------------------
    |
    | PowerGrid supports Tailwind and Bootstrap 5 themes.
    | Configure here the theme of your choice.
    */

    'theme' => \PowerComponents\LivewirePowerGrid\Themes\Tailwind::class,
    //'theme' => \PowerComponents\LivewirePowerGrid\Themes\Bootstrap5::class,

    /*
    |--------------------------------------------------------------------------
    | Plugins
    |--------------------------------------------------------------------------
    |
    | Plugins used: bootstrap-select when bootstrap, flatpicker.js to datepicker.
    |
    */

    'plugins' => [
        /*
         * https://github.com/snapappointments/bootstrap-select
         */
        'bootstrap-select' => [
            'js'  => 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.14.0-beta2/js/bootstrap-select.min.js',
            'css' => 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.14.0-beta2/css/bootstrap-select.min.css',
        ],
        /*
         * https://flatpickr.js.org
         */
        'flat_piker' => [
            'js'        => 'https://cdn.jsdelivr.net/npm/flatpickr',
            'css'       => 'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css',
            'translate' => (app()->getLocale() != 'en') ? 'https://npmcdn.com/flatpickr/dist/l10n/' . \Illuminate\Support\Str::substr(app()->getLocale(), 0, 2) . '.js' : '',
            'locales'   => [
                'pt_BR' => [
                    'locale'     => 'pt',
                    'dateFormat' => 'd/m/Y H:i',
                    'enableTime' => true,
                    'time_24hr'  => true,
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Filters
    |--------------------------------------------------------------------------
    |
    | PowerGrid supports inline and outside filters.
    | 'inline': Filters data inside the table.
    | 'outside': Filters data outside the table.
    | 'null'
    |
    */

    'filter' => 'inline',

    /*
    |--------------------------------------------------------------------------
    | Cache
    |--------------------------------------------------------------------------
    |
    | Cache is enabled by default to improve search performance when using collections.
    | When enabled, data is reloaded whenever the page is refreshed or a field is updated.
    |
    */

    'cached_data' => true,

    /*
    |--------------------------------------------------------------------------
    | JS Framework
    |--------------------------------------------------------------------------
    |
    | Define here which JS framework will be imported in the views.
    | Alpine JS is required for features like ClickToEdit and Toggleable.
    |
    */

    'js_framework' => 'alpinejs',
    //'js_framework' => null, // If you already have Alpine included in your project

    /*
    |--------------------------------------------------------------------------
    | Frameworks CDN
    |--------------------------------------------------------------------------
    |
    | Define here the CDN source for imported JS Framework
    |
    */

    'js_framework_cdn' => [
        'alpinejs' => 'https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js',
        //'alpinejs' => 'https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.8.2/dist/alpine.min.js' //Alpine 2.8
    ],

    /*
    |--------------------------------------------------------------------------
    | New Release Notification
    |--------------------------------------------------------------------------
    |
    | PowerGrid can verify if a new release is available when you create a new PowerGrid Table.
    |
    | This feature depends on composer/composer.
    | To install, run: `composer require composer/composer --dev`
    |
    */

    'check_version' => false,

];
