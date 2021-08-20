<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Template
    |--------------------------------------------------------------------------
    |
    | You will be able to know which theme pattern will be loaded
    |
    */
    'theme' => \PowerComponents\LivewirePowerGrid\Themes\Tailwind::class,

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
            'js'  => 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.14.0-beta2/js/bootstrap-select.min.js',
            'css' => 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.14.0-beta2/css/bootstrap-select.min.css'
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
                    'time_24hr'  => true
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
    | JS Framework
    |--------------------------------------------------------------------------
    |
    | Define here which JS framework will be imported in the views.
    | Alpine JS is required for features like ClickToEdit and Toggleable.
    |
    | 'js_framework' => 'alpinejs',
    */

    'js_framework' => 'alpinejs',

    /*
    |--------------------------------------------------------------------------
    | Frameworks CDN
    |--------------------------------------------------------------------------
    |
    | Define here the CDN source for imported JS Framework
    |
    */
    'js_framework_cdn' => [
        'alpinejs' => 'https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.8.2/dist/alpine.min.js'
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification latest version
    |--------------------------------------------------------------------------
    |
    | Add the package: `composer require composer/composer --dev` to your project.
    | and change this value to `true`
    |
    */
    'check_version' => false

];
