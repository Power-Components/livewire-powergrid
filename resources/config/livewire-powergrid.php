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
    | Max per page
    |--------------------------------------------------------------------------
    |
    | Upper bound for the number of rows fetched per page. The per-page value
    | travels in the component state, so this ceiling keeps a single request
    | from loading an unbounded number of rows. Set to 0 to disable the limit.
    |
    */

    'max_per_page' => 1000,

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
    | PowerGrid supports inline, dropdown and flyout filters.
    | 'inline': Filters data inside the table.
    | 'dropdown': Filters in a popover anchored to a Filter button, committed on Apply.
    | 'flyout': Filters in a drawer sliding in from the side, committed on Apply.
    | 'null'
    |
    | Both 'dropdown' and 'flyout' hold edits in a draft and only commit when the
    | user presses "Apply filters" (no live/debounce requests while typing).
    |
    */

    'filter' => 'inline',

    /*
    |--------------------------------------------------------------------------
    | Filter Flyout
    |--------------------------------------------------------------------------
    |
    | Settings for the drawer used when 'filter' is set to 'flyout'.
    | 'position': which edge the drawer slides in from ('left' or 'right').
    | 'close_on_escape': close the drawer when the Escape key is pressed.
    | 'close_on_click_outside': close the drawer when its backdrop is clicked.
    |
    | Override these per table by calling config() in the table's boot() method.
    |
    */

    'filter_flyout' => [
        'position' => 'right',
        'close_on_escape' => true,
        'close_on_click_outside' => true,
    ],

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
