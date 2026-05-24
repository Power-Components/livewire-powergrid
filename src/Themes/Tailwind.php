<?php

namespace PowerComponents\LivewirePowerGrid\Themes;

class Tailwind extends Theme
{
    protected ?string $parentTheme = null;

    /** @lang Tailwind CSS */
    public function struct(): Components\ThemeBuilder
    {
        return Components\ThemeBuilder::make($this->name())
            ->baseView('livewire-powergrid::components.themes.tailwind')
            ->layout(fn (Components\Layout $layout) => $layout
                ->wrapper('space-y-4')
                ->outsideFilters('')
            )
            ->header(fn (Components\Header $header) => $header
                ->view('header')
                ->layout(fn (Components\Layout $layout) => $layout
                    ->container('md:flex md:flex-row w-full justify-between items-center mb-3')
                    ->subContainer('md:flex md:flex-row w-full gap-1')
                    ->actionsContainer('flex flex-row items-center text-sm flex-wrap')
                    ->actions('focus:ring-accent focus-within:focus:ring-accent focus-within:ring-accent dark:focus-within:ring-accent flex rounded-md ring-1 transition focus-within:ring-2 dark:ring-zinc-600 dark:text-zinc-300 text-zinc-600 ring-zinc-300 dark:bg-zinc-800 bg-white dark:placeholder-zinc-400 rounded-md border-0 bg-transparent py-2 px-3 ring-0 placeholder:text-zinc-400 focus:outline-none sm:text-sm sm:leading-6 w-auto')
                )
                ->searchBox(fn (Components\SearchBox $searchBox) => $searchBox
                    ->view('header.search')
                    ->container('flex flex-row mt-3 md:mt-0 w-full rounded-full flex justify-start sm:justify-center md:justify-end')
                    ->relativeMain('group relative rounded-full w-full md:w-4/12 float-end float-right md:w-full lg:w-1/2')
                    ->input('focus:ring-accent focus-within:focus:ring-accent focus-within:ring-accent dark:focus-within:ring-accent flex items-center rounded-md ring-1 transition focus-within:ring-2 dark:ring-zinc-600 dark:text-zinc-300 text-zinc-600 ring-zinc-300 dark:bg-zinc-800 bg-white dark:placeholder-zinc-400 w-full rounded-md border-0 bg-transparent py-1.5 px-2 ring-0 placeholder:text-zinc-400 focus:outline-none sm:text-sm sm:leading-6 w-full pl-8')
                    ->iconSearchWrapper('absolute inset-y-0 left-0 flex items-center h-full pl-3 pointer-events-none')
                    ->iconCloseWrapper('absolute opacity-0 group-hover:opacity-100 transition-all inset-y-0 right-0 flex items-center pr-1')
                    ->iconClose('text-zinc-400 dark:text-zinc-200')
                    ->iconSearch('text-zinc-300 mr-2 w-5 h-5 dark:text-zinc-200')
                )
            )
            ->table(fn (Components\Table $table) => $table
                ->layout(fn (Components\Layout $layout) => $layout
                    ->container('rounded-t-lg relative border-x border-t border-zinc-200 dark:bg-zinc-700 dark:border-zinc-600')
                    ->table('min-w-full dark:!bg-zinc-800')
                    ->thead('shadow-sm rounded-t-lg bg-zinc-100 dark:bg-zinc-900')
                    ->tr('border-b border-zinc-100 dark:border-zinc-600 hover:bg-zinc-50 dark:bg-zinc-800 dark:hover:bg-zinc-700')
                    ->th('font-extrabold px-3 py-3 text-left text-xs text-zinc-700 tracking-wider whitespace-nowrap dark:text-zinc-300')
                    ->thActions('font-extrabold px-3 py-3 text-end text-xs text-zinc-700 tracking-wider whitespace-nowrap dark:text-zinc-300')
                    ->tbody('text-zinc-800')
                    ->td('px-3 py-2 whitespace-nowrap dark:text-zinc-200')
                    ->tdActions('px-3 py-2 whitespace-nowrap dark:text-zinc-200 text-end')
                )
                ->body(fn (Components\Body $body) => $body
                    ->tr(fn (Components\Tr $tr) => $tr
                        ->responsive('text-zinc-500 border-zinc-100 dark:text-zinc-200 break-words w-full text-sm')
                        ->responsiveToggleIcon('text-zinc-600 w-5 h-5 transition-all duration-300 dark:text-zinc-200')
                    )
                )
                ->checkbox(fn (Components\Checkbox $checkbox) => $checkbox
                    ->th('px-6 py-3 text-left text-xs font-medium text-zinc-500 tracking-wider')
                    ->base('')
                    ->label('flex items-center space-x-3')
                    ->input('form-checkbox dark:border-zinc-600 border-1 dark:bg-zinc-800 rounded border-zinc-300 bg-white transition duration-100 ease-in-out h-4 w-4 text-accent focus:ring-accent dark:ring-offset-zinc-900')
                )
                ->radio(fn (Components\Radio $radio) => $radio
                    ->th('px-6 py-3 text-left text-xs font-medium text-zinc-500 tracking-wider')
                    ->base('')
                    ->label('flex items-center space-x-3')
                    ->input('form-radio rounded-full transition ease-in-out duration-100')
                )
            )
            ->cols(fn (Components\Cols $cols) => $cols
                ->div('flex items-center gap-1')
            )
            ->footer(fn (Components\Footer $footer) => $footer
                ->view('footer')
                ->layout(fn (Components\Layout $layout) => $layout
                    ->container('border-x border-b rounded-b-lg border-b border-zinc-200 dark:bg-zinc-700 dark:border-zinc-600')
                    ->select('focus:ring-accent focus-within:focus:ring-accent focus-within:ring-accent dark:focus-within:ring-accent rounded-md ring-1 transition focus-within:ring-2 dark:ring-zinc-600 dark:text-zinc-300 text-zinc-600 ring-zinc-300 dark:bg-zinc-800 bg-white dark:placeholder-zinc-400 rounded-md border-0 bg-transparent py-1.5 px-3 pr-8 ring-0 placeholder:text-zinc-400 focus:outline-none sm:text-sm sm:leading-6 w-auto')
                )
                ->pagination('pagination')
            );
    }

    public function editable(): array
    {
        return [
            'editable' => (new Components\Component())
                ->view('livewire-powergrid::components.themes.tailwind.editable')
                ->clickable('py-2')
                ->input('focus:ring-accent focus-within:focus:ring-accent focus-within:ring-accent dark:focus-within:ring-accent flex rounded-md ring-1 transition focus-within:ring-2 dark:ring-zinc-600 dark:text-zinc-300 text-zinc-600 ring-zinc-300 dark:bg-zinc-800 bg-white dark:placeholder-zinc-400 w-full rounded-md border-0 bg-transparent py-1.5 px-2 ring-0 placeholder:text-zinc-400 focus:outline-none sm:text-sm sm:leading-6 w-full')
                ->error('text-sm text-red-800 p-1 transition-all duration-200')
                ->toArray(),
        ];
    }

    public function toggleable(): array
    {
        return [
            'toggleable' => (new Components\Component())
                ->view('livewire-powergrid::components.themes.tailwind.toggleable')
                ->toArray(),
        ];
    }

    public function filter(): array
    {
        return [
            'filter' => [
                'label' => 'block text-sm font-medium text-zinc-700 dark:text-zinc-300',
                'boolean' => [
                    'view' => 'livewire-powergrid::components.themes.tailwind.filters.boolean',
                    'base' => 'min-w-[5rem]',
                    'select' => 'focus:ring-accent focus-within:focus:ring-accent focus-within:ring-accent dark:focus-within:ring-accent rounded-md ring-1 transition focus-within:ring-2 dark:ring-zinc-600 dark:text-zinc-300 text-zinc-600 ring-zinc-300 dark:bg-zinc-800 bg-white dark:placeholder-zinc-400 w-full rounded-md border-0 bg-transparent py-1.5 px-3 pr-8 ring-0 placeholder:text-zinc-400 focus:outline-none sm:text-sm sm:leading-6 w-full',
                ],
                'date_picker' => [
                    'base' => '',
                    'view' => 'powergrid-plugins::Flatpickr.index',
                    'input' => 'flatpickr flatpickr-input focus:ring-accent focus-within:focus:ring-accent focus-within:ring-accent dark:focus-within:ring-accent flex rounded-md ring-1 transition focus-within:ring-2 dark:ring-zinc-600 dark:text-zinc-300 text-zinc-600 ring-zinc-300 dark:bg-zinc-800 bg-white dark:placeholder-zinc-400 w-full rounded-md border-0 bg-transparent py-1.5 px-2 ring-0 placeholder:text-zinc-400 focus:outline-none sm:text-sm sm:leading-6 w-auto',
                ],
                'multi_select' => [
                    'view' => 'livewire-powergrid::components.themes.tailwind.filters.multi-select',
                    'base' => 'inline-block relative w-full',
                    'select' => 'mt-1',
                ],
                'number' => [
                    'view' => 'livewire-powergrid::components.themes.tailwind.filters.number',
                    'base' => '',
                    'input' => 'w-full min-w-[5rem] block focus:ring-accent focus-within:focus:ring-accent focus-within:ring-accent dark:focus-within:ring-accent flex rounded-md ring-1 transition focus-within:ring-2 dark:ring-zinc-600 dark:text-zinc-300 text-zinc-600 ring-zinc-300 dark:bg-zinc-800 bg-white dark:placeholder-zinc-400 rounded-md border-0 bg-transparent py-1.5 pl-2 ring-0 placeholder:text-zinc-400 focus:outline-none sm:text-sm sm:leading-6',
                ],
                'select' => [
                    'view' => 'livewire-powergrid::components.themes.tailwind.filters.select',
                    'base' => '',
                    'select' => 'focus:ring-accent focus-within:focus:ring-accent focus-within:ring-accent dark:focus-within:ring-accent rounded-md ring-1 transition focus-within:ring-2 dark:ring-zinc-600 dark:text-zinc-300 text-zinc-600 ring-zinc-300 dark:bg-zinc-800 bg-white dark:placeholder-zinc-400 rounded-md border-0 bg-transparent py-1.5 px-3 pr-8 ring-0 placeholder:text-zinc-400 focus:outline-none sm:text-sm sm:leading-6 w-full',
                ],
                'input_text' => [
                    'view' => 'livewire-powergrid::components.themes.tailwind.filters.input-text',
                    'base' => 'min-w-[9.5rem]',
                    'select' => 'focus:ring-accent focus-within:focus:ring-accent focus-within:ring-accent dark:focus-within:ring-accent rounded-md ring-1 transition focus-within:ring-2 dark:ring-zinc-600 dark:text-zinc-300 text-zinc-600 ring-zinc-300 dark:bg-zinc-800 bg-white dark:placeholder-zinc-400 w-full rounded-md border-0 bg-transparent py-1.5 px-3 pr-8 ring-0 placeholder:text-zinc-400 focus:outline-none sm:text-sm sm:leading-6 w-full',
                    'input' => 'focus:ring-accent focus-within:focus:ring-accent focus-within:ring-accent dark:focus-within:ring-accent flex rounded-md ring-1 transition focus-within:ring-2 dark:ring-zinc-600 dark:text-zinc-300 text-zinc-600 ring-zinc-300 dark:bg-zinc-800 bg-white dark:placeholder-zinc-400 w-full rounded-md border-0 bg-transparent py-1.5 px-2 ring-0 placeholder:text-zinc-400 focus:outline-none sm:text-sm sm:leading-6 w-full',
                ],
                'input' => 'focus:ring-accent focus-within:focus:ring-accent focus-within:ring-accent dark:focus-within:ring-accent flex rounded-md ring-1 transition focus-within:ring-2 dark:ring-zinc-600 dark:text-zinc-300 text-zinc-600 ring-zinc-300 dark:bg-zinc-800 bg-white dark:placeholder-zinc-400 w-full rounded-md border-0 bg-transparent py-1.5 px-2 ring-0 placeholder:text-zinc-400 focus:outline-none sm:text-sm sm:leading-6 w-full',
            ],
        ];
    }
}
