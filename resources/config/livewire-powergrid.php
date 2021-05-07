<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Template
    |--------------------------------------------------------------------------
    |
    | You will be able to know which theme pattern will be loaded
    | bootstrap or tailwind
    |
    */
    'theme' => 'tailwind',

    /*
    |--------------------------------------------------------------------------
    | Theme Version
    |--------------------------------------------------------------------------
    |
    | Here you define which version and bootstrap or tailwind you are using
    | >= bootstrap@5.0.0-beta3 working with bootstrap-select
    |
    */

    'theme_versions' => [
        'bootstrap' => '50',
        /*
         * https://tailwindcss.com
         */
        'tailwind' => '2'
    ],

    /*
    |--------------------------------------------------------------------------
    | Plugins
    |--------------------------------------------------------------------------
    |
    | Plugins used: bootstrap-select when bootstrap, flatpicker.js to datepicker
    |
    */
    'plugins' => [
        /*
         * https://github.com/snapappointments/bootstrap-select
         */
        'bootstrap-select' => [
            'js' => 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.14.0-beta2/js/bootstrap-select.min.js',
            'css' => 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.14.0-beta2/css/bootstrap-select.min.css'
        ],
        /*
         * https://flatpickr.js.org
         */
        'flat_piker' => [
            'js' => 'https://cdn.jsdelivr.net/npm/flatpickr',
            'css' => 'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css',
            'translate' => (app()->getLocale() != 'en') ? 'https://npmcdn.com/flatpickr/dist/l10n/'.\Illuminate\Support\Str::substr(app()->getLocale(), 0, 2).'.js': '',
            'locales' => [
                'pt_BR' => [
                    'locale' => 'pt',
                    'dateFormat' => 'd/m/Y H:i',
                    'enableTime' => true,
                    'time_24hr' => true
                ]
            ]
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Filters
    |--------------------------------------------------------------------------
    |
    | You choose which type of filter you want to use inline
    | to filter inside the table or outside the table
    | 'inline' or 'outside'
    |
    */

    'filter' => 'inline',

    /*
    |--------------------------------------------------------------------------
    | Cache
    |--------------------------------------------------------------------------
    |
    | When the data is cached, the search is much faster.
    | It is updated whenever the page is reloaded or a field is changed
    |
    */
    'cached_data' => true,

    /*
    |--------------------------------------------------------------------------
    | Framework JS
    |--------------------------------------------------------------------------
    |
    | Here you define which js framework will be used on views
    | For example: alpinejs
    |
    */
    'framework_js' => null,

    /*
    |--------------------------------------------------------------------------
    | Frameworks CDN
    |--------------------------------------------------------------------------
    |
    | Here you define what version will be used
    |
    */
    'frameworks_cdn' => [
        'alpinejs' => 'https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.8.2/dist/alpine.min.js'
    ]

];
