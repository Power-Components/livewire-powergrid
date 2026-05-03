<?php

namespace PowerComponents\LivewirePowerGrid\Themes;

class DaisyUI extends Theme
{
    public function views(): array
    {
        return [
            'header' => 'livewire-powergrid::components.frameworks.daisyui.header',
            'header.export' => 'livewire-powergrid::components.frameworks.daisyui.header.export',
            'header.toggle-columns' => 'livewire-powergrid::components.frameworks.daisyui.header.toggle-columns',
            'header.soft-deletes' => 'livewire-powergrid::components.frameworks.daisyui.header.soft-deletes',
            'header.search' => 'livewire-powergrid::components.frameworks.daisyui.header.search',
            'header.filters' => 'livewire-powergrid::components.frameworks.daisyui.header.filters',
            'pagination' => 'livewire-powergrid::components.frameworks.daisyui.pagination',
        ];
    }

    // -----------------------------------------------------------------------
    // Unified token struct
    // -----------------------------------------------------------------------

    public function struct(): array
    {
        return [
            // Kept for back-compat: data_get($theme, 'root') and data_get($theme, 'name')
            // are used extensively in existing Blade views (header.blade.php, row.blade.php, etc.).
            'name' => 'daisyui',
            'root' => 'livewire-powergrid::components.frameworks.tailwind',

            'table' => [
                'base' => 'table table-zebra',
                'layout' => [
                    'base' => 'p-3 align-middle inline-block min-w-full w-full sm:px-6 lg:px-8',
                    'div' => 'rounded-t-lg relative border-x border-t border-base-300',
                    'container' => '-my-2 overflow-x-auto sm:-mx-3 lg:-mx-8',
                    'actions' => 'gap-2',
                ],

                'header' => [
                    'thead' => 'text-base-content !capitalize',
                    'tr' => 'bg-base-200',
                    'th' => '',
                    'th_wrapper' => 'flex items-center gap-1',
                    'th_action' => '',
                ],

                'body' => [
                    'wrapper' => '',
                    'empty_state' => '',
                    'tr' => [
                        'wrapper' => '',
                        'summarize' => '',
                        'responsive' => 'text-base-content border-base-200 break-words w-full text-sm',
                        'filters' => '',
                    ],
                    'td' => [
                        'wrapper' => '',
                        'empty_state' => '',
                        'summarize' => [
                            'wrapper' => '',
                        ],
                        'filters' => '',
                        'actions_wrapper' => 'flex gap-2',
                    ],
                ],

                'footer' => [
                    'tr' => '',
                ],
            ],

            'footer' => [
                'view' => 'livewire-powergrid::components.frameworks.tailwind.footer',
                'select' => 'select select-bordered select-sm pr-7 w-auto',
                'footer' => 'p-4 flex flex-col md:flex-row md:items-center justify-between gap-4 border-t border-base-200',
                'footer_with_pagination' => 'p-4 flex flex-col md:flex-row md:items-center justify-between gap-4 border-t border-base-200',
            ],

            'layout' => [
                'table' => 'livewire-powergrid::components.frameworks.tailwind.table-base',
                'header' => 'livewire-powergrid::components.frameworks.tailwind.header',
                'pagination' => 'livewire-powergrid::components.frameworks.tailwind.pagination',
                'footer' => 'livewire-powergrid::components.frameworks.tailwind.footer',
            ],

            'cols' => [
                'div' => 'select-none flex items-center gap-1 !text-base-content',
            ],

            'editable' => [
                'view' => 'livewire-powergrid::components.frameworks.tailwind.editable',
                'clickable' => 'py-2',
                'input' => 'input input-bordered input-sm w-full',
                'error' => 'text-sm text-error p-1 transition-all duration-200',
            ],

            'toggleable' => [
                'view' => 'livewire-powergrid::components.frameworks.tailwind.toggleable',
            ],

            'checkbox' => [
                'th' => 'px-6 py-3 text-left text-xs font-medium tracking-wider',
                'base' => '',
                'label' => 'flex items-center space-x-3',
                'input' => 'checkbox checkbox-sm',
            ],

            'radio' => [
                'th' => 'px-6 py-3 text-left text-xs font-medium tracking-wider',
                'base' => '',
                'label' => 'flex items-center space-x-3',
                'input' => 'radio',
            ],

            'filter' => [
                'label' => 'block text-sm font-semibold !text-base-content opacity-80',
                'boolean' => [
                    'view' => 'livewire-powergrid::components.frameworks.tailwind.filters.boolean',
                    'base' => 'min-w-[5rem]',
                    'select' => 'select select-sm select-bordered w-full',
                ],
                'date_picker' => [
                    'base' => '',
                    'view' => 'livewire-powergrid::components.frameworks.tailwind.filters.date-picker',
                    'input' => 'flatpickr flatpickr-input input input-sm input-bordered w-full',
                ],
                'multi_select' => [
                    'view' => 'livewire-powergrid::components.frameworks.tailwind.filters.multi-select',
                    'base' => 'inline-block relative w-full',
                    'select' => 'select select-sm select-bordered w-full mt-1',
                ],
                'number' => [
                    'view' => 'livewire-powergrid::components.frameworks.tailwind.filters.number',
                    'base' => '',
                    'input' => 'w-full min-w-[5rem] block input input-sm input-bordered',
                ],
                'select' => [
                    'view' => 'livewire-powergrid::components.frameworks.tailwind.filters.select',
                    'base' => '',
                    'select' => 'select select-sm select-bordered w-full',
                ],
                'input_text' => [
                    'view' => 'livewire-powergrid::components.frameworks.tailwind.filters.input-text',
                    'base' => 'min-w-[9.5rem]',
                    'select' => 'select select-sm select-bordered w-full',
                    'input' => 'input input-sm input-bordered w-full',
                ],
                'input' => 'input input-sm',
            ],

            'header' => [
                'container' => 'mb-3 md:flex md:flex-row w-full justify-between items-center',
                'sub_container' => 'md:flex md:flex-row w-full gap-1',
                'actions' => 'flex flex-row items-center text-sm flex-wrap gap-2',

                'batch_exporting' => [
                    'container' => 'w-full my-3 p-4 rounded-lg bg-base-200 text-center',
                    'progress_bar' => 'progress progress-primary w-full h-2',
                    'finished_container' => 'w-full my-3 bg-base-100 border border-base-300 rounded-lg',
                    'finished_button' => 'btn btn-ghost btn-sm w-full justify-between',
                ],

                'export' => [
                    'container' => 'dropdown',
                    'button' => 'btn btn-ghost btn-sm border-base-300',
                    'menu' => 'dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-max mt-2',
                    'menu_item' => '',
                    'menu_button' => '',
                ],

                'toggle_columns' => [
                    'container' => 'dropdown',
                    'button' => 'btn btn-ghost btn-sm border-base-300',
                    'menu' => 'dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-52 mt-2',
                    'menu_item' => '',
                ],

                'soft_deletes' => [
                    'container' => 'dropdown',
                    'button' => 'btn btn-ghost btn-sm border-base-300',
                    'menu' => 'dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-52 mt-2',
                    'menu_item' => '',
                    'menu_button' => '',
                ],

                'filters' => [
                    'container' => 'flex mt-2 sm:mt-0 gap-3',
                    'button' => 'btn btn-ghost btn-sm border-base-300',
                ],

                'enabled_filters' => [
                    'container' => 'flex flex-wrap gap-2 mt-2',
                    'clear_all_button' => 'badge badge-error gap-1 cursor-pointer py-3',
                    'filter_button' => 'badge badge-outline gap-1 cursor-pointer py-3',
                ],
            ],

            'search_box' => [
                'container' => 'w-full md:w-auto mt-2 md:mt-0',
                'relative_main' => '',
                'input' => 'input input-bordered input-sm w-full md:w-80',
                'icon_close' => 'text-base-content/50',
                'icon_search' => 'text-base-content/50 h-4 w-4',
            ],
        ];
    }
}
