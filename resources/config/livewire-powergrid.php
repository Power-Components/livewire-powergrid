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
            'translate' => 'https://npmcdn.com/flatpickr/dist/l10n/pt.js'
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

    'filter' => 'inline'
];
