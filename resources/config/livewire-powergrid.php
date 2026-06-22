<?php

use PowerComponents\LivewirePowerGrid\FilterAttributes\{Boolean, InputText, Number, Select};
use PowerComponents\LivewirePowerGrid\Plugins\Export\OpenSpout\v5\{ExportToCsv, ExportToXLS};
use PowerComponents\LivewirePowerGrid\Themes\{DaisyUI, Flux, Tailwind};

return [

    /*
    |--------------------------------------------------------------------------
    | Theme
    |--------------------------------------------------------------------------
    |
    | PowerGrid supports Tailwind.
    | Configure here the theme of your choice.
    */

    'theme' => Tailwind::class,
    // 'theme' => DaisyUI::class,
    // 'theme' => Flux::class,

    'cache_ttl' => null,

    /*
    |--------------------------------------------------------------------------
    | Plugins
    |--------------------------------------------------------------------------
    |
    | Plugins used: flatpickr.js to datepicker.
    |
    */

    'plugins' => [
        /*
         * https://flatpickr.js.org
         */
        'flatpickr' => [
            'locales' => [
                'pt_BR' => [
                    'locale' => 'pt',
                    'dateFormat' => 'd/m/Y H:i',
                    'enableTime' => true,
                    'time_24hr' => true,
                ],
            ],
        ],

        'select' => [
            'default' => 'slim',
            'slim' => [
                'cdn' => 'https://unpkg.com/slim-select@2.9.1/dist/slimselect.min.js',
                'css' => 'https://unpkg.com/slim-select@2.9.1/dist/slimselect.css',
            ],
            'tom' => [
                'cdn' => 'https://cdn.jsdelivr.net/npm/tom-select@2.4.1/dist/js/tom-select.complete.min.js',
                'css' => 'https://cdn.jsdelivr.net/npm/tom-select@2.4.1/dist/css/tom-select.css',
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
    | Filters Attributes
    |--------------------------------------------------------------------------

    | You can add custom attributes to the filters.
    | The key is the filter type and the value is a callback function.
    | like: input_text, select, datetime, etc.
    | The callback function receives the field and title as parameters.
    | The callback function must return an array with the attributes.
    */

    'filter_attributes' => [
        'input_text' => InputText::class,
        'boolean' => Boolean::class,
        'number' => Number::class,
        'select' => Select::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Persisting
    |--------------------------------------------------------------------------
    |
    | PowerGrid supports persisting of the filters, columns and sorting.
    | 'session': persist in the session.
    | 'cache': persist with cache.
    | 'cookies': persist with cookies (default).
    |
    */

    'persist_driver' => 'cookies',

    /*
    |--------------------------------------------------------------------------
    | Exportable class
    |--------------------------------------------------------------------------
    |
    |
    */

    'exportable' => [
        'default' => 'openspout_v5',
        'openspout_v5' => [
            'xlsx' => ExportToXLS::class,
            'csv' => ExportToCsv::class,
        ],
        'openspout_v4' => [
            'xlsx' => PowerComponents\LivewirePowerGrid\Plugins\Export\OpenSpout\v4\ExportToXLS::class,
            'csv' => PowerComponents\LivewirePowerGrid\Plugins\Export\OpenSpout\v4\ExportToCsv::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Auto-Discover Models
    |--------------------------------------------------------------------------
    |
    | PowerGrid will search for Models in the directories listed below.
    | These Models be listed as options when you run the
    | "artisan powergrid:create" command.
    |
    */

    'auto_discover_models_paths' => [
        app_path('Models'),
    ],
];
