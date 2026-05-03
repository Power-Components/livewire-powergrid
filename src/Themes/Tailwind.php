<?php

namespace PowerComponents\LivewirePowerGrid\Themes;

class Tailwind extends Theme
{
    public function views(): array
    {
        return [
            'header' => 'livewire-powergrid::components.frameworks.tailwind.header',
            'footer' => 'livewire-powergrid::components.frameworks.tailwind.footer',
            'table' => 'livewire-powergrid::components.frameworks.tailwind.table-base',
            'pagination' => 'livewire-powergrid::components.frameworks.tailwind.pagination',
            'editable' => 'livewire-powergrid::components.frameworks.tailwind.editable',
            'toggleable' => 'livewire-powergrid::components.frameworks.tailwind.toggleable',
            'toggle-detail' => 'livewire-powergrid::components.frameworks.tailwind.toggle-detail',
            'toggle-detail-responsive' => 'livewire-powergrid::components.frameworks.tailwind.toggle-detail-responsive',
            'filter.boolean' => 'livewire-powergrid::components.frameworks.tailwind.filters.boolean',
            'filter.date_picker' => 'livewire-powergrid::components.frameworks.tailwind.filters.date-picker',
            'filter.multi_select' => 'livewire-powergrid::components.frameworks.tailwind.filters.multi-select',
            'filter.number' => 'livewire-powergrid::components.frameworks.tailwind.filters.number',
            'filter.select' => 'livewire-powergrid::components.frameworks.tailwind.filters.select',
            'filter.input_text' => 'livewire-powergrid::components.frameworks.tailwind.filters.input-text',
            'header.export' => 'livewire-powergrid::components.frameworks.tailwind.header.export',
            'header.toggle-columns' => 'livewire-powergrid::components.frameworks.tailwind.header.toggle-columns',
            'header.soft-deletes' => 'livewire-powergrid::components.frameworks.tailwind.header.soft-deletes',
            'header.filters' => 'livewire-powergrid::components.frameworks.tailwind.header.filters',
            'header.loading' => 'livewire-powergrid::components.frameworks.tailwind.header.loading',
            'header.search' => 'livewire-powergrid::components.frameworks.tailwind.header.search',
            'header.enabled-filters' => 'livewire-powergrid::components.frameworks.tailwind.header.enabled-filters',
            'header.batch-exporting' => 'livewire-powergrid::components.frameworks.tailwind.header.batch-exporting',
            'header.multi-sort' => 'livewire-powergrid::components.frameworks.tailwind.header.multi-sort',
            'header.message-soft-deletes' => 'livewire-powergrid::components.frameworks.tailwind.header.message-soft-deletes',

            'table.header' => 'livewire-powergrid::components.frameworks.tailwind.table.tr',
            'table.row' => 'livewire-powergrid::components.frameworks.tailwind.table.row',
            'table.cols' => 'livewire-powergrid::components.frameworks.tailwind.table.cols',
            'table.th-empty' => 'livewire-powergrid::components.frameworks.tailwind.table.th-empty',
            'table.inline-filters' => 'livewire-powergrid::components.frameworks.tailwind.table.inline-filters',
            'table.header-summarize' => 'livewire-powergrid::components.frameworks.tailwind.table.header-summarize',
            'table.footer-summarize' => 'livewire-powergrid::components.frameworks.tailwind.table.footer-summarize',
            'table.responsive-container' => 'livewire-powergrid::components.frameworks.tailwind.table.responsive-container',
            'table.detail' => 'livewire-powergrid::components.frameworks.tailwind.table.detail',
            'table.summarize' => 'livewire-powergrid::components.frameworks.tailwind.table.summarize',
            'table.checkbox-all' => 'livewire-powergrid::components.frameworks.tailwind.table.checkbox-all',
            'table.checkbox-row' => 'livewire-powergrid::components.frameworks.tailwind.table.checkbox-row',
            'table.radio-row' => 'livewire-powergrid::components.frameworks.tailwind.table.radio-row',
        ];
    }

    /** @lang Tailwind CSS */
    public function struct(): array
    {
        return [
            'name' => 'tailwind',
            'root' => 'livewire-powergrid::components.frameworks.tailwind',

            'header' => [
                'container' => 'mb-3 md:flex md:flex-row w-full justify-between items-center',
                'sub_container' => 'md:flex md:flex-row w-full gap-1',
                'actions' => 'flex flex-row items-center text-sm flex-wrap',

                'batch_exporting' => [
                    'container' => 'w-full my-3 px-4 rounded dark:text-pg-primary-300 bg-pg-primary-100 dark:bg-pg-primary-800 py-3 text-center',
                    'progress_bar' => 'bg-emerald-500 rounded h-1 text-center',
                    'finished_container' => 'w-full my-3 dark:bg-pg-primary-800',
                    'finished_button' => 'appearance-none text-left text-base font-medium text-pg-primary-500 focus:outline-none dark:text-pg-primary-300',
                ],

                'export' => [
                    'container' => 'relative',
                    'button' => 'focus:ring-primary-600 focus-within:focus:ring-primary-600 focus-within:ring-primary-600 dark:focus-within:ring-primary-600 flex rounded-md ring-1 transition focus-within:ring-2 dark:ring-pg-primary-600 dark:text-pg-primary-300 text-gray-600 ring-gray-300 dark:bg-pg-primary-800 bg-white dark:placeholder-pg-primary-400 rounded-md border-0 bg-transparent py-2 px-3 ring-0 placeholder:text-gray-400 focus:outline-none sm:text-sm sm:leading-6 w-auto',
                    'menu' => 'absolute z-10 mt-2 rounded-md dark:bg-pg-primary-700 bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none',
                    'menu_item' => 'flex items-center px-4 py-1 text-pg-primary-400 dark:text-pg-primary-300 border-b border-pg-primary-100 dark:border-pg-primary-600',
                    'menu_button' => 'px-2 py-1 block text-pg-primary-800 hover:bg-pg-primary-100 hover:text-black-300 dark:text-pg-primary-200 dark:hover:bg-pg-primary-800 rounded',
                ],

                'toggle_columns' => [
                    'container' => 'relative',
                    'button' => 'focus:ring-primary-600 focus-within:focus:ring-primary-600 focus-within:ring-primary-600 dark:focus-within:ring-primary-600 flex rounded-md ring-1 transition focus-within:ring-2 dark:ring-pg-primary-600 dark:text-pg-primary-300 text-gray-600 ring-gray-300 dark:bg-pg-primary-800 bg-white dark:placeholder-pg-primary-400 rounded-md border-0 bg-transparent py-2 px-3 ring-0 placeholder:text-gray-400 focus:outline-none sm:text-sm sm:leading-6 w-auto',
                    'menu' => 'absolute z-10 mt-2 rounded-md dark:bg-pg-primary-700 bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none w-48',
                    'menu_item' => 'block px-4 py-2 text-sm text-pg-primary-700 hover:bg-pg-primary-100 hover:text-pg-primary-900 dark:text-pg-primary-200 dark:hover:bg-pg-primary-800',
                ],

                'soft_deletes' => [
                    'container' => 'relative',
                    'button' => 'focus:ring-primary-600 focus-within:focus:ring-primary-600 focus-within:ring-primary-600 dark:focus-within:ring-primary-600 flex rounded-md ring-1 transition focus-within:ring-2 dark:ring-pg-primary-600 dark:text-pg-primary-300 text-gray-600 ring-gray-300 dark:bg-pg-primary-800 bg-white dark:placeholder-pg-primary-400 rounded-md border-0 bg-transparent py-2 px-3 ring-0 placeholder:text-gray-400 focus:outline-none sm:text-sm sm:leading-6 w-auto',
                    'menu' => 'absolute z-10 mt-2 rounded-md dark:bg-pg-primary-700 bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none w-48',
                    'menu_item' => 'block px-4 py-2 text-sm text-pg-primary-700 hover:bg-pg-primary-100 hover:text-pg-primary-900 dark:text-pg-primary-200 dark:hover:bg-pg-primary-800',
                    'menu_button' => 'px-2 py-1 block text-pg-primary-800 hover:bg-pg-primary-100 hover:text-black-300 dark:text-pg-primary-200 dark:hover:bg-pg-primary-800 rounded',
                ],

                'filters' => [
                    'container' => 'flex mr-2 mt-2 sm:mt-0 gap-3',
                    'button' => 'focus:ring-primary-600 focus-within:focus:ring-primary-600 focus-within:ring-primary-600 dark:focus-within:ring-primary-600 flex rounded-md ring-1 transition focus-within:ring-2 dark:ring-pg-primary-600 dark:text-pg-primary-300 text-gray-600 ring-gray-300 dark:bg-pg-primary-800 bg-white dark:placeholder-pg-primary-400 rounded-md border-0 bg-transparent py-2 px-3 ring-0 placeholder:text-gray-400 focus:outline-none sm:text-sm sm:leading-6 w-auto',
                ],

                'enabled_filters' => [
                    'container' => 'pg-enabled-filters-base',
                    'clear_all_button' => 'select-none rounded-md outline-none inline-flex items-center border px-2 py-0.5 font-bold text-xs border-pg-primary-500 bg-pg-primary-100 dark:border-pg-primary-500 dark:bg-pg-primary-900 dark:text-pg-primary-300 dark:hover:text-pg-primary-400 text-pg-primary-600 hover:text-pg-primary-500',
                    'filter_button' => 'select-none rounded-md outline-none inline-flex items-center border px-2 py-0.5 font-bold text-xs border-pg-primary-300 bg-white dark:border-pg-primary-600 dark:bg-pg-primary-800 dark:text-pg-primary-300 dark:hover:text-pg-primary-400 text-pg-primary-600 hover:text-pg-primary-500',
                ],
            ],

            'table' => [
                'base' => 'min-w-full dark:!bg-primary-800',
                'layout' => [
                    'base' => 'p-3 align-middle inline-block min-w-full w-full sm:px-6 lg:px-8',
                    'div' => 'rounded-t-lg relative border-x border-t border-pg-primary-200 dark:bg-pg-primary-700 dark:border-pg-primary-600',
                    'container' => '-my-2 overflow-x-auto sm:-mx-3 lg:-mx-8',
                    'actions' => 'flex gap-2',
                ],

                'header' => [
                    'thead' => 'shadow-sm rounded-t-lg bg-pg-primary-100 dark:bg-pg-primary-900',
                    'tr' => '',
                    'th' => 'font-extrabold px-3 py-3 text-left text-xs text-pg-primary-700 tracking-wider whitespace-nowrap dark:text-pg-primary-300',
                    'th_wrapper' => 'flex items-center gap-1',
                    'th_action' => '!font-bold',
                ],

                'body' => [
                    'wrapper' => 'text-pg-primary-800',
                    'empty_state' => '',
                    'tr' => [
                        'wrapper' => 'border-b border-pg-primary-100 dark:border-pg-primary-600 hover:bg-pg-primary-50 dark:bg-pg-primary-800 dark:hover:bg-pg-primary-700',
                        'summarize' => '',
                        'responsive' => 'text-pg-primary-500 border-pg-primary-100 dark:text-pg-primary-200 break-words w-full text-sm',
                        'filters' => '',
                    ],
                    'td' => [
                        'wrapper' => 'px-3 py-2 whitespace-nowrap dark:text-pg-primary-200',
                        'empty_state' => 'p-2 whitespace-nowrap dark:text-pg-primary-200',
                        'summarize' => [
                            'wrapper' => 'p-2 whitespace-nowrap dark:text-pg-primary-200 text-sm text-pg-primary-600 text-right space-y-2',
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
                'select' => 'focus:ring-primary-600 focus-within:focus:ring-primary-600 focus-within:ring-primary-600 dark:focus-within:ring-primary-600 rounded-md ring-1 transition focus-within:ring-2 dark:ring-pg-primary-600 dark:text-pg-primary-300 text-gray-600 ring-gray-300 dark:bg-pg-primary-800 bg-white dark:placeholder-pg-primary-400 rounded-md border-0 bg-transparent py-1.5 px-3 pr-8 ring-0 placeholder:text-gray-400 focus:outline-none sm:text-sm sm:leading-6 w-auto',
                'footer' => 'border-x border-b rounded-b-lg border-b border-pg-primary-200 dark:bg-pg-primary-700 dark:border-pg-primary-600',
                'footer_with_pagination' => 'md:flex md:flex-row w-full items-center py-3 bg-white overflow-y-auto pl-2 pr-2 relative dark:bg-pg-primary-900',
            ],

            'layout' => [
                'table' => 'livewire-powergrid::components.frameworks.tailwind.table-base',
                'header' => 'livewire-powergrid::components.frameworks.tailwind.header',
                'pagination' => 'livewire-powergrid::components.frameworks.tailwind.pagination',
                'footer' => 'livewire-powergrid::components.frameworks.tailwind.footer',
            ],

            'cols' => [
                'div' => 'select-none flex items-center gap-1',
            ],

            'editable' => [
                'view' => 'livewire-powergrid::components.frameworks.tailwind.editable',
                'clickable' => 'py-2',
                'input' => 'focus:ring-primary-600 focus-within:focus:ring-primary-600 focus-within:ring-primary-600 dark:focus-within:ring-primary-600 flex rounded-md ring-1 transition focus-within:ring-2 dark:ring-pg-primary-600 dark:text-pg-primary-300 text-gray-600 ring-gray-300 dark:bg-pg-primary-800 bg-white dark:placeholder-pg-primary-400 w-full rounded-md border-0 bg-transparent py-1.5 px-2 ring-0 placeholder:text-gray-400 focus:outline-none sm:text-sm sm:leading-6 w-full',
                'error' => 'text-sm text-red-800 p-1 transition-all duration-200',
            ],

            'toggleable' => [
                'view' => 'livewire-powergrid::components.frameworks.tailwind.toggleable',
            ],

            'checkbox' => [
                'th' => 'px-6 py-3 text-left text-xs font-medium text-pg-primary-500 tracking-wider',
                'base' => '',
                'label' => 'flex items-center space-x-3',
                'input' => 'form-checkbox dark:border-dark-600 border-1 dark:bg-dark-800 rounded border-gray-300 bg-white transition duration-100 ease-in-out h-4 w-4 text-primary-500 focus:ring-primary-500 dark:ring-offset-dark-900',
            ],

            'radio' => [
                'th' => 'px-6 py-3 text-left text-xs font-medium text-pg-primary-500 tracking-wider',
                'base' => '',
                'label' => 'flex items-center space-x-3',
                'input' => 'form-radio rounded-full transition ease-in-out duration-100',
            ],

            'filter' => [
                'label' => 'block text-sm font-medium text-pg-primary-700 dark:text-pg-primary-300',
                'boolean' => [
                    'view' => 'livewire-powergrid::components.frameworks.tailwind.filters.boolean',
                    'base' => 'min-w-[5rem]',
                    'select' => 'focus:ring-primary-600 focus-within:focus:ring-primary-600 focus-within:ring-primary-600 dark:focus-within:ring-primary-600 rounded-md ring-1 transition focus-within:ring-2 dark:ring-pg-primary-600 dark:text-pg-primary-300 text-gray-600 ring-gray-300 dark:bg-pg-primary-800 bg-white dark:placeholder-pg-primary-400 w-full rounded-md border-0 bg-transparent py-1.5 px-3 pr-8 ring-0 placeholder:text-gray-400 focus:outline-none sm:text-sm sm:leading-6 w-full',
                ],
                'date_picker' => [
                    'base' => '',
                    'view' => 'livewire-powergrid::components.frameworks.tailwind.filters.date-picker',
                    'input' => 'flatpickr flatpickr-input focus:ring-primary-600 focus-within:focus:ring-primary-600 focus-within:ring-primary-600 dark:focus-within:ring-primary-600 flex rounded-md ring-1 transition focus-within:ring-2 dark:ring-pg-primary-600 dark:text-pg-primary-300 text-gray-600 ring-gray-300 dark:bg-pg-primary-800 bg-white dark:placeholder-pg-primary-400 w-full rounded-md border-0 bg-transparent py-1.5 px-2 ring-0 placeholder:text-gray-400 focus:outline-none sm:text-sm sm:leading-6 w-auto',
                ],
                'multi_select' => [
                    'view' => 'livewire-powergrid::components.frameworks.tailwind.filters.multi-select',
                    'base' => 'inline-block relative w-full',
                    'select' => 'mt-1',
                ],
                'number' => [
                    'view' => 'livewire-powergrid::components.frameworks.tailwind.filters.number',
                    'base' => '',
                    'input' => 'w-full min-w-[5rem] block focus:ring-primary-600 focus-within:focus:ring-primary-600 focus-within:ring-primary-600 dark:focus-within:ring-primary-600 flex rounded-md ring-1 transition focus-within:ring-2 dark:ring-pg-primary-600 dark:text-pg-primary-300 text-gray-600 ring-gray-300 dark:bg-pg-primary-800 bg-white dark:placeholder-pg-primary-400 rounded-md border-0 bg-transparent py-1.5 pl-2 ring-0 placeholder:text-gray-400 focus:outline-none sm:text-sm sm:leading-6',
                ],
                'select' => [
                    'view' => 'livewire-powergrid::components.frameworks.tailwind.filters.select',
                    'base' => '',
                    'select' => 'focus:ring-primary-600 focus-within:focus:ring-primary-600 focus-within:ring-primary-600 dark:focus-within:ring-primary-600 rounded-md ring-1 transition focus-within:ring-2 dark:ring-pg-primary-600 dark:text-pg-primary-300 text-gray-600 ring-gray-300 dark:bg-pg-primary-800 bg-white dark:placeholder-pg-primary-400 rounded-md border-0 bg-transparent py-1.5 px-3 pr-8 ring-0 placeholder:text-gray-400 focus:outline-none sm:text-sm sm:leading-6 w-full',
                ],
                'input_text' => [
                    'view' => 'livewire-powergrid::components.frameworks.tailwind.filters.input-text',
                    'base' => 'min-w-[9.5rem]',
                    'select' => 'focus:ring-primary-600 focus-within:focus:ring-primary-600 focus-within:ring-primary-600 dark:focus-within:ring-primary-600 rounded-md ring-1 transition focus-within:ring-2 dark:ring-pg-primary-600 dark:text-pg-primary-300 text-gray-600 ring-gray-300 dark:bg-pg-primary-800 bg-white dark:placeholder-pg-primary-400 w-full rounded-md border-0 bg-transparent py-1.5 px-3 pr-8 ring-0 placeholder:text-gray-400 focus:outline-none sm:text-sm sm:leading-6 w-full',
                    'input' => 'focus:ring-primary-600 focus-within:focus:ring-primary-600 focus-within:ring-primary-600 dark:focus-within:ring-primary-600 flex rounded-md ring-1 transition focus-within:ring-2 dark:ring-pg-primary-600 dark:text-pg-primary-300 text-gray-600 ring-gray-300 dark:bg-pg-primary-800 bg-white dark:placeholder-pg-primary-400 w-full rounded-md border-0 bg-transparent py-1.5 px-2 ring-0 placeholder:text-gray-400 focus:outline-none sm:text-sm sm:leading-6 w-full',
                ],
                'input' => 'focus:ring-primary-600 focus-within:focus:ring-primary-600 focus-within:ring-primary-600 dark:focus-within:ring-primary-600 flex rounded-md ring-1 transition focus-within:ring-2 dark:ring-pg-primary-600 dark:text-pg-primary-300 text-gray-600 ring-gray-300 dark:bg-pg-primary-800 bg-white dark:placeholder-pg-primary-400 w-full rounded-md border-0 bg-transparent py-1.5 px-2 ring-0 placeholder:text-gray-400 focus:outline-none sm:text-sm sm:leading-6 w-full',
            ],

            'search_box' => [
                'container' => 'flex flex-row mt-3 md:mt-0 w-full rounded-full flex justify-start sm:justify-center md:justify-end',
                'relative_main' => 'group relative rounded-full w-full md:w-4/12 float-end float-right md:w-full lg:w-1/2',
                'input' => 'focus:ring-primary-600 focus-within:focus:ring-primary-600 focus-within:ring-primary-600 dark:focus-within:ring-primary-600 flex items-center rounded-md ring-1 transition focus-within:ring-2 dark:ring-pg-primary-600 dark:text-pg-primary-300 text-gray-600 ring-gray-300 dark:bg-pg-primary-800 bg-white dark:placeholder-pg-primary-400 w-full rounded-md border-0 bg-transparent py-1.5 px-2 ring-0 placeholder:text-gray-400 focus:outline-none sm:text-sm sm:leading-6 w-full pl-8',
                'icon_search_wrapper' => 'absolute inset-y-0 left-0 flex items-center h-full pl-3 pointer-events-none',
                'icon_close_wrapper' => 'absolute opacity-0 group-hover:opacity-100 transition-all inset-y-0 right-0 flex items-center pr-1',
                'icon_close' => 'text-pg-primary-400 dark:text-pg-primary-200',
                'icon_search' => 'text-pg-primary-300 mr-2 w-5 h-5 dark:text-pg-primary-200',
            ],
        ];
    }
}
