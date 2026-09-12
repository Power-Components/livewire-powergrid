<?php

use PowerComponents\LivewirePowerGrid\Plugins\Export\OpenSpout\v5\{ExportToCsv, ExportToXLS};

return [

    /*
    |--------------------------------------------------------------------------
    | Theme
    |--------------------------------------------------------------------------
    |
    | PowerGrid ships Tailwind, DaisyUI and Flux. Configure the theme of your
    | choice by registered name ('tailwind', 'daisyui', 'flux') or by FQCN.
    */

    'theme' => 'tailwind',
    // 'theme' => 'daisyui',
    // 'theme' => 'flux',
    // 'theme' => \PowerComponents\LivewirePowerGrid\Themes\Tailwind::class, // FQCN also works

    /*
    |--------------------------------------------------------------------------
    | Theme overrides (no-code)
    |--------------------------------------------------------------------------
    |
    | Restyle any theme token without writing a Theme class. These values are
    | merged on top of the active theme with the highest precedence, using the
    | same nested token keys the theme defines. Example:
    |
    |   'theme_overrides' => [
    |       'table' => ['layout' => ['th' => 'font-bold px-4 py-3']],
    |   ],
    |
    */

    'theme_overrides' => [],

    'cache_ttl' => null,

    /*
    |--------------------------------------------------------------------------
    | Front-end assets
    |--------------------------------------------------------------------------
    |
    | Import the PowerGrid JS through your bundler (Vite):
    |
    |   // resources/js/app.js  (Tailwind / DaisyUI)
    |   import "../../vendor/power-components/livewire-powergrid/resources/js/powergrid.js";
    |
    |   // Flux — ships its own dropdown JS
    |   import "../../vendor/power-components/livewire-powergrid/resources/js/powergrid-flux.js";
    |
    */

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
    | Filter Wire Bindings
    |--------------------------------------------------------------------------
    |
    | How each filter control binds to the filter bag. The value is the Livewire
    | modifier chain applied to wire:model — 'live.debounce.600ms', 'live',
    | 'blur', 'lazy', or '' for a plain wire:model (a JS widget pushes its own
    | event). Slots: value, operator, start, end, formatted.
    |
    | Only the entries you set are overridden. A single grid can override these
    | by implementing filterWire() on the component.
    |
    */

    'filter_wire' => [
        // 'input_text' => ['value' => 'live.debounce.800ms'],
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
