<?php

namespace PowerComponents\LivewirePowerGrid\Themes;

class Tailwind extends Theme
{
    protected ?string $parentTheme = null;

    /** @lang Tailwind CSS */
    public function struct(): array
    {
        return Components\ThemeBuilder::make($this->name())
            ->baseView('livewire-powergrid::components.frameworks.tailwind')
            ->header(fn (Components\Header $header) => $header
                ->view('header')
                ->layout(fn (Components\Layout $layout) => $layout
                    ->container('mb-3 md:flex md:flex-row w-full justify-between items-center')
                    ->subContainer('md:flex md:flex-row w-full gap-1')
                    ->actionsContainer('flex flex-row items-center text-sm flex-wrap')
                    ->actions('focus:ring-primary-600 focus-within:focus:ring-primary-600 focus-within:ring-primary-600 dark:focus-within:ring-primary-600 flex rounded-md ring-1 transition focus-within:ring-2 dark:ring-pg-primary-600 dark:text-pg-primary-300 text-gray-600 ring-gray-300 dark:bg-pg-primary-800 bg-white dark:placeholder-pg-primary-400 rounded-md border-0 bg-transparent py-2 px-3 ring-0 placeholder:text-gray-400 focus:outline-none sm:text-sm sm:leading-6 w-auto')
                )
                ->searchBox(fn (Components\Component $searchBox) => $searchBox
                    ->view('header.search')
                    ->container('flex flex-row mt-3 md:mt-0 w-full rounded-full flex justify-start sm:justify-center md:justify-end')
                    ->relativeMain('group relative rounded-full w-full md:w-4/12 float-end float-right md:w-full lg:w-1/2')
                    ->input('focus:ring-primary-600 focus-within:focus:ring-primary-600 focus-within:ring-primary-600 dark:focus-within:ring-primary-600 flex items-center rounded-md ring-1 transition focus-within:ring-2 dark:ring-pg-primary-600 dark:text-pg-primary-300 text-gray-600 ring-gray-300 dark:bg-pg-primary-800 bg-white dark:placeholder-pg-primary-400 w-full rounded-md border-0 bg-transparent py-1.5 px-2 ring-0 placeholder:text-gray-400 focus:outline-none sm:text-sm sm:leading-6 w-full pl-8')
                    ->iconSearchWrapper('absolute inset-y-0 left-0 flex items-center h-full pl-3 pointer-events-none')
                    ->iconCloseWrapper('absolute opacity-0 group-hover:opacity-100 transition-all inset-y-0 right-0 flex items-center pr-1')
                    ->iconClose('text-pg-primary-400 dark:text-pg-primary-200')
                    ->iconSearch('text-pg-primary-300 mr-2 w-5 h-5 dark:text-pg-primary-200')
                )
            )
            ->table(fn (Components\Table $table) => $table
                ->view('table-base')
                ->viewHeader('table.tr')
                ->viewRow('table.row')
                ->viewCols('table.cols')
                ->viewThEmpty('table.th-empty')
                ->viewInlineFilters('table.inline-filters')
                ->viewCheckboxAll('table.checkbox-all')
                ->viewCheckboxRow('table.checkbox-row')
                ->viewRadioRow('table.radio-row')
                ->layout(fn (Components\Layout $layout) => $layout
                    ->container('rounded-t-lg relative border-x border-t border-pg-primary-200 dark:bg-pg-primary-700 dark:border-pg-primary-600')
                    ->table('min-w-full dark:!bg-primary-800')
                    ->thead('shadow-sm rounded-t-lg bg-pg-primary-100 dark:bg-pg-primary-900')
                    ->tr('border-b border-pg-primary-100 dark:border-pg-primary-600 hover:bg-pg-primary-50 dark:bg-pg-primary-800 dark:hover:bg-pg-primary-700')
                    ->th('font-extrabold px-3 py-3 text-left text-xs text-pg-primary-700 tracking-wider whitespace-nowrap dark:text-pg-primary-300')
                    ->tbody('text-pg-primary-800')
                    ->td('px-3 py-2 whitespace-nowrap dark:text-pg-primary-200')
                )
                ->checkbox(fn (Components\Component $checkbox) => $checkbox
                    ->th('px-6 py-3 text-left text-xs font-medium text-pg-primary-500 tracking-wider')
                    ->base('')
                    ->label('flex items-center space-x-3')
                    ->input('form-checkbox dark:border-dark-600 border-1 dark:bg-dark-800 rounded border-gray-300 bg-white transition duration-100 ease-in-out h-4 w-4 text-primary-500 focus:ring-primary-500 dark:ring-offset-dark-900')
                )
                ->radio(fn (Components\Component $radio) => $radio
                    ->th('px-6 py-3 text-left text-xs font-medium text-pg-primary-500 tracking-wider')
                    ->base('')
                    ->label('flex items-center space-x-3')
                    ->input('form-radio rounded-full transition ease-in-out duration-100')
                )
            )
            ->footer(fn (Components\Footer $footer) => $footer
                ->view('footer')
                ->layout(fn (Components\Layout $layout) => $layout
                    ->container('border-x border-b rounded-b-lg border-b border-pg-primary-200 dark:bg-pg-primary-700 dark:border-pg-primary-600')
                    ->select('focus:ring-primary-600 focus-within:focus:ring-primary-600 focus-within:ring-primary-600 dark:focus-within:ring-primary-600 rounded-md ring-1 transition focus-within:ring-2 dark:ring-pg-primary-600 dark:text-pg-primary-300 text-gray-600 ring-gray-300 dark:bg-pg-primary-800 bg-white dark:placeholder-pg-primary-400 rounded-md border-0 bg-transparent py-1.5 px-3 pr-8 ring-0 placeholder:text-gray-400 focus:outline-none sm:text-sm sm:leading-6 w-auto')
                )
                ->pagination(fn (Components\Component $pagination) => $pagination
                    ->view('pagination')
                )
            )
            ->toArray();
    }

    public function editable(): array
    {
        return [
            'editable' => (new Components\Component())
                ->view('livewire-powergrid::components.frameworks.tailwind.editable')
                ->clickable('py-2')
                ->input('focus:ring-primary-600 focus-within:focus:ring-primary-600 focus-within:ring-primary-600 dark:focus-within:ring-primary-600 flex rounded-md ring-1 transition focus-within:ring-2 dark:ring-pg-primary-600 dark:text-pg-primary-300 text-gray-600 ring-gray-300 dark:bg-pg-primary-800 bg-white dark:placeholder-pg-primary-400 w-full rounded-md border-0 bg-transparent py-1.5 px-2 ring-0 placeholder:text-gray-400 focus:outline-none sm:text-sm sm:leading-6 w-full')
                ->error('text-sm text-red-800 p-1 transition-all duration-200')
                ->toArray(),
        ];
    }

    public function toggleable(): array
    {
        return [
            'toggleable' => (new Components\Component())
                ->view('livewire-powergrid::components.frameworks.tailwind.toggleable')
                ->toArray(),
        ];
    }

    public function filter(): array
    {
        return [
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
        ];
    }
}
