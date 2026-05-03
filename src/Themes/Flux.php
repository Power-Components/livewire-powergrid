<?php

namespace PowerComponents\LivewirePowerGrid\Themes;

class Flux extends Theme
{
    public function views(): array
    {
        return [
            'pagination' => 'livewire-powergrid::components.frameworks.flux.pagination',
            'header.export' => 'livewire-powergrid::components.frameworks.flux.header.export',
            'header.toggle-columns' => 'livewire-powergrid::components.frameworks.flux.header.toggle-columns',
            'header.soft-deletes' => 'livewire-powergrid::components.frameworks.flux.header.soft-deletes',
            // 'editable'   => 'livewire-powergrid::components.frameworks.flux.editable',
            // 'toggleable' => 'livewire-powergrid::components.frameworks.flux.toggleable',
        ];
    }

    public function struct(): array
    {
        $input = 'shadow-xs rounded-lg border border-zinc-200 border-b-zinc-300/80 dark:border-white/10 bg-white dark:bg-white/10 text-zinc-700 dark:text-zinc-300 placeholder-zinc-400 dark:placeholder-zinc-400 text-sm py-2 px-3 focus:outline-none';
        $button = 'inline-flex items-center gap-2 rounded-lg border border-zinc-200 border-b-zinc-300/80 dark:border-zinc-600 bg-white dark:bg-zinc-700 shadow-xs px-3 py-2 text-sm font-medium text-zinc-800 dark:text-white hover:bg-zinc-50 dark:hover:bg-zinc-600/75 focus:outline-none cursor-pointer transition-colors';

        return [
            'name' => 'flux',
            'root' => 'livewire-powergrid::components.frameworks.tailwind',

            'header' => [
                'container' => 'mb-3 md:flex md:flex-row w-full justify-between items-center',
                'sub_container' => 'md:flex md:flex-row w-full gap-1.5',
                'actions' => 'flex flex-row items-center text-sm flex-wrap',

                'batch_exporting' => [
                    'container' => 'w-full my-3 px-4 rounded-lg dark:text-zinc-300 bg-zinc-100 dark:bg-zinc-800 py-3 text-center',
                    'progress_bar' => 'bg-emerald-500 rounded-full h-1 text-center',
                    'finished_container' => 'w-full my-3 dark:bg-zinc-800',
                    'finished_button' => 'appearance-none text-left text-sm font-medium text-zinc-500 focus:outline-none dark:text-zinc-300',
                ],

                'export' => [
                    'container' => 'relative',
                    'button' => $button,
                    'menu' => 'absolute z-10 mt-1.5 rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 shadow-lg focus:outline-none',
                    'menu_item' => 'flex items-center px-3 py-1.5 text-zinc-400 dark:text-zinc-400 border-b border-zinc-100 dark:border-zinc-700',
                    'menu_button' => 'px-3 py-1.5 block w-full text-left text-sm text-zinc-700 dark:text-zinc-200 hover:bg-zinc-50 dark:hover:bg-zinc-700 transition-colors',
                ],

                'toggle_columns' => [
                    'container' => 'relative',
                    'button' => $button,
                    'menu' => 'absolute z-10 mt-1.5 rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 shadow-lg focus:outline-none w-48',
                    'menu_item' => 'block px-3 py-2 text-sm text-zinc-700 dark:text-zinc-200 hover:bg-zinc-50 dark:hover:bg-zinc-700 transition-colors',
                ],

                'soft_deletes' => [
                    'container' => 'relative',
                    'button' => $button,
                    'menu' => 'absolute z-10 mt-1.5 rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 shadow-lg focus:outline-none w-48',
                    'menu_item' => 'block px-3 py-2 text-sm text-zinc-700 dark:text-zinc-200 hover:bg-zinc-50 dark:hover:bg-zinc-700 transition-colors',
                    'menu_button' => 'px-3 py-1.5 block w-full text-left text-sm text-zinc-700 dark:text-zinc-200 hover:bg-zinc-50 dark:hover:bg-zinc-700 transition-colors',
                ],

                'filters' => [
                    'container' => 'flex mr-2 mt-2 sm:mt-0 gap-3',
                    'button' => $button,
                ],

                'enabled_filters' => [
                    'container' => 'pg-enabled-filters-base',
                    'clear_all_button' => 'select-none rounded-lg outline-none inline-flex items-center gap-1 border px-2 py-0.5 text-xs font-medium border-zinc-300 dark:border-zinc-600 bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-zinc-700 transition-colors',
                    'filter_button' => 'select-none rounded-lg outline-none inline-flex items-center gap-1 border px-2 py-0.5 text-xs font-medium border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-700 transition-colors',
                ],
            ],

            'table' => [
                'base' => 'min-w-full',
                'layout' => [
                    'base' => 'p-3 align-middle inline-block min-w-full w-full sm:px-6 lg:px-8',
                    'div' => 'rounded-t-lg relative border border-zinc-200 dark:border-zinc-700 dark:bg-zinc-900',
                    'container' => '-my-2 overflow-x-auto sm:-mx-3 lg:-mx-8',
                    'actions' => 'flex gap-2',
                ],

                'header' => [
                    'thead' => 'bg-zinc-50 dark:bg-zinc-800',
                    'tr' => '',
                    'th' => 'font-semibold px-3 py-3 text-left text-xs text-zinc-500 tracking-wider whitespace-nowrap dark:text-zinc-400',
                    'th_wrapper' => 'flex items-center gap-1',
                    'th_action' => '',
                ],

                'body' => [
                    'wrapper' => 'text-zinc-800 dark:text-zinc-200',
                    'empty_state' => '',
                    'tr' => [
                        'wrapper' => 'border-b border-zinc-100 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-800/60',
                        'summarize' => '',
                        'responsive' => 'text-zinc-500 border-zinc-100 dark:text-zinc-400 break-words w-full text-sm',
                        'filters' => '',
                    ],
                    'td' => [
                        'wrapper' => 'px-3 py-2 whitespace-nowrap',
                        'empty_state' => 'p-2 whitespace-nowrap',
                        'summarize' => [
                            'wrapper' => 'p-2 whitespace-nowrap dark:text-zinc-200 text-sm text-zinc-500 text-right space-y-2',
                        ],
                        'filters' => '',
                        'actions_wrapper' => 'flex gap-2',
                    ],
                ],

                'footer' => [
                    'tr' => '',
                ],
            ],

            'pagination' => [
                'wrapper' => 'items-center justify-between sm:flex gap-2',
                'count_wrapper' => 'items-center justify-between w-full sm:flex-1 sm:flex',
                'count_text' => 'text-sm text-zinc-500 dark:text-zinc-400 leading-5 text-center sm:text-left',
                'count_value' => 'font-semibold text-zinc-700 dark:text-zinc-200',
                'nav' => 'items-center justify-end sm:flex',
                'nav_buttons' => 'flex items-center gap-1 justify-center mt-2 md:flex-none md:justify-end sm:mt-0',
                'nav_buttons_simple' => 'flex items-center justify-center gap-1.5 md:flex-none md:justify-end sm:mt-0',
                'button' => 'cursor-pointer select-none inline-flex items-center justify-center h-8 w-8 rounded-lg border border-zinc-200 border-b-zinc-300/80 dark:border-zinc-600 bg-white dark:bg-zinc-700 shadow-xs text-zinc-700 dark:text-zinc-200 hover:bg-zinc-50 dark:hover:bg-zinc-600/75 transition-colors focus:outline-none',
                'button_text' => 'cursor-pointer select-none inline-flex items-center justify-center h-8 px-3 rounded-lg border border-zinc-200 border-b-zinc-300/80 dark:border-zinc-600 bg-white dark:bg-zinc-700 shadow-xs text-sm font-medium text-zinc-700 dark:text-zinc-200 hover:bg-zinc-50 dark:hover:bg-zinc-600/75 transition-colors focus:outline-none',
                'button_disabled' => 'select-none inline-flex items-center justify-center h-8 px-3 rounded-lg border border-zinc-200 border-b-zinc-300/80 dark:border-zinc-600 bg-white dark:bg-zinc-700 shadow-xs text-sm font-medium text-zinc-400 dark:text-zinc-500 opacity-50 cursor-default',
                'page_active' => 'select-none inline-flex items-center justify-center h-8 min-w-8 px-2 rounded-lg border border-zinc-800 dark:border-zinc-200 bg-zinc-800 dark:bg-zinc-200 text-white dark:text-zinc-800 text-sm font-semibold shadow-xs',
                'page_inactive' => 'cursor-pointer select-none inline-flex items-center justify-center h-8 min-w-8 px-2 rounded-lg border border-zinc-200 border-b-zinc-300/80 dark:border-zinc-600 bg-white dark:bg-zinc-700 shadow-xs text-sm text-zinc-700 dark:text-zinc-200 hover:bg-zinc-50 dark:hover:bg-zinc-600/75 transition-colors focus:outline-none',
            ],

            'footer' => [
                'view' => 'livewire-powergrid::components.frameworks.tailwind.footer',
                'select' => 'shadow-xs rounded-lg border border-zinc-200 border-b-zinc-300/80 dark:border-white/10 bg-white dark:bg-white/10 text-zinc-700 dark:text-zinc-300 text-sm py-2 px-3 pr-8 focus:outline-none',
                'footer' => 'border border-t-0 rounded-b-lg border-zinc-200 dark:border-zinc-700 dark:bg-zinc-900',
                'footer_with_pagination' => 'md:flex md:flex-row w-full items-center py-3 bg-white dark:bg-zinc-900 overflow-y-auto px-3 relative gap-4',
            ],

            'layout' => [
                'table' => 'livewire-powergrid::components.frameworks.tailwind.table-base',
                'header' => 'livewire-powergrid::components.frameworks.tailwind.header',
                'pagination' => 'livewire-powergrid::components.frameworks.flux.pagination',
                'footer' => 'livewire-powergrid::components.frameworks.tailwind.footer',
            ],

            'cols' => [
                'div' => 'select-none flex items-center gap-1',
            ],

            'editable' => [
                'view' => 'livewire-powergrid::components.frameworks.tailwind.editable',
                'clickable' => 'py-2',
                'input' => $input.' w-full',
                'error' => 'text-sm text-red-500 p-1 transition-all duration-200',
            ],

            'toggleable' => [
                'view' => 'livewire-powergrid::components.frameworks.tailwind.toggleable',
            ],

            'checkbox' => [
                'th' => 'px-6 py-3 text-left text-xs font-medium text-zinc-500 tracking-wider',
                'base' => '',
                'label' => 'flex items-center space-x-3',
                'input' => 'rounded border-zinc-200 dark:border-white/10 bg-white dark:bg-white/10 h-4 w-4 text-zinc-800 focus:ring-zinc-500',
            ],

            'radio' => [
                'th' => 'px-6 py-3 text-left text-xs font-medium text-zinc-500 tracking-wider',
                'base' => '',
                'label' => 'flex items-center space-x-3',
                'input' => 'rounded-full border-zinc-200 dark:border-white/10 text-zinc-800 focus:ring-zinc-500',
            ],

            'filter' => [
                'label' => 'block text-xs font-medium text-zinc-500 dark:text-zinc-400',
                'boolean' => [
                    'view' => 'livewire-powergrid::components.frameworks.tailwind.filters.boolean',
                    'base' => 'min-w-[5rem]',
                    'select' => $input.' w-full pr-8',
                ],
                'date_picker' => [
                    'base' => '',
                    'view' => 'livewire-powergrid::components.frameworks.tailwind.filters.date-picker',
                    'input' => 'flatpickr flatpickr-input '.$input.' w-auto',
                ],
                'multi_select' => [
                    'view' => 'livewire-powergrid::components.frameworks.tailwind.filters.multi-select',
                    'base' => 'inline-block relative w-full',
                    'select' => 'mt-1',
                ],
                'number' => [
                    'view' => 'livewire-powergrid::components.frameworks.tailwind.filters.number',
                    'base' => '',
                    'input' => $input.' w-full min-w-[5rem] block',
                ],
                'select' => [
                    'view' => 'livewire-powergrid::components.frameworks.tailwind.filters.select',
                    'base' => '',
                    'select' => $input.' w-full pr-8',
                ],
                'input_text' => [
                    'view' => 'livewire-powergrid::components.frameworks.tailwind.filters.input-text',
                    'base' => 'min-w-[9.5rem]',
                    'select' => $input.' w-full pr-8',
                    'input' => $input.' w-full',
                ],
                'input' => $input.' w-full',
            ],

            'search_box' => [
                'container' => 'flex flex-row mt-3 md:mt-0 w-full rounded-full flex justify-start sm:justify-center md:justify-end',
                'relative_main' => 'group relative rounded-full w-full md:w-4/12 float-end float-right md:w-full lg:w-1/2 flex items-center',
                'input' => $input.' w-full pl-10 pr-3 py-2',
                'icon_search_wrapper' => 'absolute inset-y-0 left-0 flex items-center h-full pl-3 pointer-events-none',
                'icon_close_wrapper' => 'absolute opacity-0 group-hover:opacity-100 transition-all inset-y-0 right-0 flex items-center pr-1',
                'icon_close' => 'text-zinc-400 dark:text-zinc-500',
                'icon_search' => 'text-zinc-400 dark:text-zinc-400 w-4 h-4',
            ],
        ];
    }
}
