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
                ->card('rounded-xl border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-800 overflow-visible')
                ->outsideFilters('')
            )
            ->header(fn (Components\Header $header) => $header
                ->view('header')
                ->layout(fn (Components\Layout $layout) => $layout
                    ->container('md:flex md:flex-row w-full justify-between items-center gap-3 p-4')
                    ->subContainer('flex flex-row flex-wrap items-center gap-1')
                    ->actionsContainer('flex flex-row items-center text-sm flex-wrap')
                    ->actions($this->headerButtonClass())
                )
                ->searchBox(fn (Components\SearchBox $searchBox) => $searchBox
                    ->view('header.search')
                    ->container('flex flex-row w-full rounded-full flex justify-start sm:justify-center md:justify-end items-center')
                    ->relativeMain('group relative rounded-full w-full')
                    ->input('focus:ring-accent focus-within:focus:ring-accent focus-within:ring-accent dark:focus-within:ring-accent flex items-center rounded-md ring-1 transition focus-within:ring-2 dark:ring-zinc-600 dark:text-zinc-300 text-zinc-600 ring-zinc-300 dark:bg-zinc-800 bg-white dark:placeholder-zinc-400 w-full rounded-md border-0 bg-transparent py-1.5 pr-2 pl-8 ring-0 placeholder:text-zinc-400 focus:outline-none sm:text-sm sm:leading-6')
                    ->iconSearchWrapper('absolute inset-y-0 left-0 flex items-center h-full pl-2 pointer-events-none')
                    ->iconCloseWrapper('absolute opacity-0 group-hover:opacity-100 transition-all inset-y-0 right-0 flex items-center pr-1')
                    ->iconClose('text-zinc-400 dark:text-zinc-200')
                    ->iconSearch('text-zinc-300 mr-2 w-5 h-5 dark:text-zinc-200')
                    ->icon('livewire-powergrid::icons.search')
                    ->iconClear('livewire-powergrid::icons.x')
                )
                ->toggleColumns(fn (Components\HeaderButton $button) => $button
                    ->button($this->headerButtonClass())
                    ->iconClass('w-5 h-5 shrink-0 text-zinc-500 dark:text-zinc-300')
                    ->label('ml-2')
                    ->menu('toggle-columns-base group absolute z-10 mt-2 w-56 rounded-md dark:bg-zinc-700 bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none')
                    ->menuItem('cursor-pointer text-sm flex gap-2 justify-between block px-4 py-2 text-zinc-800 hover:bg-zinc-100 hover:text-zinc-900 dark:text-zinc-200 dark:hover:bg-zinc-800')
                )
                ->softDeletes(fn (Components\HeaderButton $button) => $button
                    ->button($this->headerButtonClass())
                    ->iconClass('w-5 h-5 shrink-0 text-zinc-500 dark:text-zinc-300')
                    ->label('ml-2')
                    ->menu('mt-2 py-2 w-48 bg-white shadow-xl absolute z-10 dark:bg-zinc-700')
                    ->menuItem('cursor-pointer flex justify-start block px-4 py-2 text-zinc-800 hover:bg-zinc-50 hover:text-zinc-800 dark:text-zinc-200 dark:hover:bg-zinc-900 dark:hover:bg-zinc-700')
                )
                ->filters(fn (Components\HeaderButton $button) => $button
                    ->wrapper('flex mt-2 sm:mt-0 gap-3')
                    ->button($this->headerButtonClass())
                    ->iconClass('w-5 h-5 shrink-0 text-zinc-500 dark:text-zinc-300')
                    ->label('ml-2')
                )
                ->filterBuilder(fn (Components\HeaderButton $button) => $button
                    ->button($this->headerButtonClass())
                    ->iconClass('h-5 w-5 shrink-0 text-zinc-500 dark:text-zinc-300')
                    ->label('ml-2')
                    ->badge('inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-blue-600 px-1.5 text-xs font-medium text-white')
                )
                ->export(fn (Components\HeaderButton $button) => $button
                    ->button($this->headerButtonClass())
                    ->iconClass('h-5 w-5 shrink-0 text-zinc-500 dark:text-zinc-300')
                    ->label('ml-2')
                    ->menu('mt-2 py-2 w-48 bg-white shadow-xl absolute z-10 dark:bg-zinc-700')
                    ->menuItem('cursor-pointer flex justify-start block px-4 py-2 text-zinc-800 hover:bg-zinc-50 hover:text-zinc-800 dark:text-zinc-200 dark:hover:bg-zinc-900 dark:hover:bg-zinc-700')
                )
                ->enabledFilters(fn (Components\HeaderButton $button) => $button
                    ->wrapper('flex group items-center gap-3 cursor-pointer')
                    ->label('text-xs font-medium text-zinc-500 dark:text-zinc-400')
                    ->iconClass('size-5')
                    ->pill('select-none rounded-md outline-none inline-flex items-center gap-1 border border-accent bg-transparent px-2 py-0.5 font-medium text-xs text-accent hover:bg-accent/10 transition-colors cursor-pointer')
                    ->pillClearAll('cursor-pointer')
                )
            )
            ->table(fn (Components\Table $table) => $table
                ->layout(fn (Components\Layout $layout) => $layout
                    ->container('overflow-x-auto relative border-t border-zinc-200 dark:bg-zinc-700 dark:border-zinc-600')
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
                    ->container('flex items-center px-3 py-2 border-t rounded-b-xl border-zinc-200 dark:bg-zinc-700 dark:border-zinc-600')
                    ->select('focus:ring-accent focus-within:focus:ring-accent focus-within:ring-accent dark:focus-within:ring-accent rounded-md ring-1 transition focus-within:ring-2 dark:ring-zinc-600 dark:text-zinc-300 text-zinc-600 ring-zinc-300 dark:bg-zinc-800 bg-white dark:placeholder-zinc-400 rounded-md border-0 bg-transparent py-1.5 px-3 pr-8 ring-0 placeholder:text-zinc-400 focus:outline-none sm:text-sm sm:leading-6 w-auto')
                )
                ->pagination('pagination')
            );
    }

    /** @return array<string, mixed> */
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

    /** @return array<string, mixed> */
    public function toggleable(): array
    {
        return [
            'toggleable' => (new Components\Component())
                ->fill([
                    'colorOn' => 'var(--color-accent, #16a34a)',
                    'colorOff' => 'var(--color-zinc-200, #e4e4e7)',
                    'colorOnDark' => 'var(--color-accent, #16a34a)',
                    'colorOffDark' => 'var(--color-zinc-600, #52525b)',
                    'knobOn' => 'var(--color-accent-foreground, #ffffff)',
                ])
                ->toArray(),
        ];
    }

    /** @return array<string, mixed> */
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
                'dropdown' => [
                    'view' => 'livewire-powergrid::components.themes.tailwind.filter',
                    'wrapper' => 'relative inline-block text-left',
                    'trigger' => $this->headerButtonClass().' relative justify-center',
                    'badge' => 'absolute -top-1.5 -right-1.5 inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-blue-600 px-1.5 text-xs font-semibold text-white',
                    'panel' => 'fixed inset-x-4 top-4 z-50 flex max-h-[calc(100dvh-2rem)] max-w-[calc(100vw-2rem)] flex-col overflow-hidden origin-top rounded-xl border border-zinc-200 bg-white shadow-lg dark:border-zinc-700 dark:bg-zinc-800 lg:absolute lg:inset-x-auto lg:top-auto lg:left-auto lg:right-0 lg:mt-2',
                    'header' => 'flex shrink-0 items-center justify-between px-4 pt-4 pb-2',
                    'title' => 'text-base font-semibold text-zinc-800 dark:text-zinc-100',
                    'body' => 'min-h-0 flex-1 overflow-y-auto px-4 py-3',
                    'grid' => 'grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4',
                    'footer' => 'flex shrink-0 items-center justify-end gap-2 border-t border-zinc-200 px-4 py-3 dark:border-zinc-700',
                    'reset' => 'text-sm font-medium text-red-500 transition hover:text-red-600',
                    'clear' => 'rounded-lg border border-zinc-200 bg-white px-3 py-1.5 text-sm font-medium text-zinc-700 transition hover:bg-zinc-50 dark:border-zinc-600 dark:bg-zinc-700 dark:text-zinc-200 dark:hover:bg-zinc-600',
                    'apply' => 'rounded-lg bg-blue-600 px-4 py-1.5 text-sm font-semibold text-white transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500/50',
                ],
                'flyout' => [
                    'view' => 'livewire-powergrid::components.themes.tailwind.filter-flyout',
                    'overlay' => 'fixed inset-0 z-40 bg-zinc-900/40',
                    'panel' => 'fixed inset-y-0 z-50 flex w-full max-w-full flex-col bg-white shadow-xl dark:bg-zinc-800 sm:w-96 sm:max-w-[90vw]',
                    'panel_left' => 'left-0 border-r border-zinc-200 dark:border-zinc-700',
                    'panel_right' => 'right-0 border-l border-zinc-200 dark:border-zinc-700',
                    'header' => 'flex items-center justify-between gap-3 border-b border-zinc-200 px-4 py-3 dark:border-zinc-700',
                    'title' => 'text-sm font-semibold text-zinc-700 dark:text-zinc-200',
                    'close' => 'rounded-md p-1 text-zinc-500 transition hover:bg-zinc-100 hover:text-zinc-700 dark:text-zinc-400 dark:hover:bg-zinc-700 dark:hover:text-zinc-200',
                    'body' => 'flex-1 overflow-y-auto px-4 py-4',
                    'footer' => 'border-t border-zinc-200 px-4 py-3 dark:border-zinc-700',
                    'clear_all' => 'w-full rounded-md border border-zinc-300 bg-white px-3 py-2 text-xs font-bold text-zinc-600 transition hover:text-zinc-500 dark:border-zinc-600 dark:bg-zinc-900 dark:text-zinc-300 dark:hover:text-zinc-400',
                ],
            ],
        ];
    }

    private function headerButtonClass(): string
    {
        return 'focus:ring-accent focus-within:focus:ring-accent focus-within:ring-accent dark:focus-within:ring-accent flex items-center justify-center rounded-md ring-1 transition focus-within:ring-2 dark:ring-zinc-600 dark:text-zinc-300 text-zinc-600 ring-zinc-300 dark:bg-zinc-800 bg-white dark:placeholder-zinc-400 rounded-md border-0 bg-transparent py-2 px-3 ring-0 placeholder:text-zinc-400 focus:outline-none sm:text-sm sm:leading-6 w-auto';
    }
}
