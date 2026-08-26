<?php

namespace PowerComponents\LivewirePowerGrid\Themes;

class Flux extends Theme
{
    protected ?string $parentTheme = Tailwind::class;

    public function struct(): Components\ThemeBuilder
    {
        return Components\ThemeBuilder::make($this->name())
            ->baseView('livewire-powergrid::components.themes.flux')
            ->layout(fn (Components\Layout $layout) => $layout
                ->wrapper('space-y-4')
                ->card('rounded-xl border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900 overflow-visible')
                ->outsideFilters('')
            )
            ->header(fn (Components\Header $header) => $header
                ->view('livewire-powergrid::components.themes.flux.header')
                ->layout(fn (Components\Layout $layout) => $layout
                    ->container('p-4 md:flex md:flex-row w-full justify-between items-center gap-3')
                    ->subContainer('flex flex-row flex-wrap items-center gap-1.5')
                    ->actionsContainer('flex flex-row items-center text-sm flex-wrap gap-2')
                    ->actions($this->button())
                )
                ->searchBox(fn (Components\SearchBox $searchBox) => $searchBox
                    ->view('livewire-powergrid::components.themes.flux.header.search')
                    ->container('flex flex-row w-full justify-start sm:justify-center md:justify-end')
                    ->relativeMain('w-full')
                    ->icon('magnifying-glass')
                    ->iconClear('')
                )
                ->toggleColumns(fn (Components\HeaderButton $button) => $button
                    ->button($this->triggerButton())
                    ->iconClass('w-5 h-5 shrink-0')
                    ->label('ml-2')
                    ->menu('dark:bg-zinc-900')
                )
                ->softDeletes(fn (Components\HeaderButton $button) => $button
                    ->button($this->triggerButton())
                    ->iconClass('w-5 h-5 shrink-0')
                    ->label('ml-2')
                    ->menu('dark:bg-zinc-900')
                )
                ->filters(fn (Components\HeaderButton $button) => $button
                    ->wrapper('flex mt-2 sm:mt-0 gap-3')
                    ->button($this->triggerButton())
                    ->iconClass('w-5 h-5 shrink-0')
                    ->label('ml-2')
                )
                ->filterBuilder(fn (Components\HeaderButton $button) => $button
                    ->button($this->triggerButton())
                    ->iconClass('w-5 h-5 shrink-0')
                    ->label('')
                    ->badge('absolute -top-1.5 -right-1.5 inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-accent px-1.5 text-xs font-semibold text-accent-foreground')
                )
                ->export(fn (Components\HeaderButton $button) => $button
                    ->button($this->triggerButton())
                    ->iconClass('w-5 h-5 shrink-0')
                    ->label('ml-2')
                    ->menu('dark:bg-zinc-900')
                )
            )
            ->table(fn (Components\Table $table) => $table
                ->layout(fn (Components\Layout $layout) => $layout
                    ->container('overflow-x-auto relative border-t border-zinc-200 dark:border-zinc-700 dark:bg-zinc-900')
                    ->table('min-w-full')
                    ->thead('bg-white dark:bg-white/10')
                    ->tr('border-b border-zinc-100 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-800/60')
                    ->th('font-semibold px-3 py-3 text-left text-xs text-zinc-500 tracking-wider whitespace-nowrap dark:text-zinc-400')
                    ->thActions('font-semibold px-3 py-3 text-end text-xs text-zinc-500 tracking-wider whitespace-nowrap dark:text-zinc-400')
                    ->tbody('text-sm text-zinc-800 dark:text-zinc-200')
                    ->td('px-3 py-2 whitespace-nowrap')
                    ->tdActions('px-3 py-2 whitespace-nowrap text-end')
                )
                ->checkbox(fn (Components\Checkbox $checkbox) => $checkbox
                    ->th('px-6 py-3 text-left text-xs font-medium text-zinc-500 tracking-wider')
                    ->input('rounded border-zinc-200 dark:border-white/10 bg-white dark:bg-white/10 h-4 w-4 text-zinc-800 focus:ring-zinc-500')
                )
                ->radio(fn (Components\Radio $radio) => $radio
                    ->th('px-6 py-3 text-left text-xs font-medium text-zinc-500 tracking-wider')
                    ->input('rounded-full border-zinc-200 dark:border-white/10 text-zinc-800 focus:ring-zinc-500')
                )
            )
            ->footer(fn (Components\Footer $footer) => $footer
                ->view('livewire-powergrid::components.themes.flux.footer')
                ->layout(fn (Components\Layout $layout) => $layout
                    ->container('flex flex-wrap items-center gap-2 overflow-hidden rounded-b-xl border-t border-zinc-200 dark:border-zinc-700 dark:bg-zinc-900 px-4 py-3')
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
                ->input($this->input().' w-full')
                ->error('text-sm text-red-500 p-1 transition-all duration-200')
                ->toArray(),
        ];
    }

    /** @return array<string, mixed> */
    public function toggleable(): array
    {
        return [
            'toggleable' => (new Components\Component())
                ->fill([
                    'colorOn' => 'var(--color-accent, #4f46e5)',
                    'colorOff' => 'var(--color-zinc-200, #e4e4e7)',
                    'colorOnDark' => 'var(--color-accent, #4f46e5)',
                    'colorOffDark' => 'var(--color-zinc-700, #3f3f46)',
                    'knobOn' => 'var(--color-accent-foreground, #ffffff)',
                ])
                ->toArray(),
        ];
    }

    /** @return array<string, mixed> */
    public function filter(): array
    {
        return [
            'filter' => (new Components\Filter())
                ->label('block text-xs font-medium text-zinc-500 dark:text-zinc-400')
                ->boolean(fn (Components\Component $boolean) => $boolean
                    ->view('livewire-powergrid::components.themes.tailwind.filters.boolean')
                    ->select($this->select().' w-full')
                )
                ->datePicker(fn (Components\Component $datePicker) => $datePicker
                    ->view('powergrid-plugins::Flatpickr.index')
                    ->input('flatpickr flatpickr-input '.$this->input().' w-full')
                )
                ->number(fn (Components\Component $number) => $number
                    ->view('livewire-powergrid::components.themes.tailwind.filters.number')
                    ->input($this->input().' w-full min-w-[5rem] block')
                )
                ->select(fn (Components\Component $select) => $select
                    ->view('livewire-powergrid::components.themes.tailwind.filters.select')
                    ->select($this->select().' w-full')
                )
                ->inputText(fn (Components\Component $inputText) => $inputText
                    ->view('livewire-powergrid::components.themes.tailwind.filters.input-text')
                    ->select($this->select().' w-full')
                    ->input($this->input().' w-full')
                )
                ->input($this->input().' w-full')
                ->dropdown(fn (Components\Dropdown $dropdown) => $dropdown
                    ->view('livewire-powergrid::components.themes.tailwind.filter')
                    ->wrapper('relative inline-block text-left')
                    ->trigger($this->triggerButton())
                    ->badge('absolute -top-1.5 -right-1.5 inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-accent px-1.5 text-xs font-semibold text-accent-foreground')
                    ->panel('fixed inset-x-4 top-4 z-50 flex max-h-[calc(100dvh-2rem)] max-w-[calc(100vw-2rem)] flex-col overflow-hidden origin-top rounded-xl border border-zinc-200 bg-white shadow-lg dark:border-zinc-700 dark:bg-zinc-900 lg:absolute lg:inset-x-auto lg:top-auto lg:left-auto lg:right-0 lg:mt-2')
                    ->header('flex shrink-0 items-center justify-between px-4 pt-4 pb-2')
                    ->title('text-base font-semibold text-zinc-800 dark:text-zinc-100')
                    ->body('min-h-0 flex-1 overflow-y-auto px-4 py-3')
                    ->grid('grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4')
                    ->footer('flex shrink-0 items-center justify-end gap-2 border-t border-zinc-200 px-4 py-3 dark:border-zinc-700')
                    ->reset('text-sm font-medium text-red-500 transition hover:text-red-600')
                    ->clear($this->button())
                    ->apply('inline-flex items-center gap-1.5 rounded-lg bg-accent px-4 py-2 text-sm font-semibold text-accent-foreground shadow-xs transition hover:bg-accent/90 focus:outline-none')
                )
                ->flyout(fn (Components\Flyout $flyout) => $flyout
                    ->overlay('fixed inset-0 z-40 bg-zinc-900/40')
                    ->panel('fixed inset-y-0 z-50 flex w-full max-w-full flex-col bg-white shadow-xl dark:bg-zinc-900 sm:w-96 sm:max-w-[90vw]')
                    ->panelLeft('left-0 border-r border-zinc-200 dark:border-zinc-700')
                    ->panelRight('right-0 border-l border-zinc-200 dark:border-zinc-700')
                    ->header('flex items-center justify-between gap-3 border-b border-zinc-200 px-4 py-3 dark:border-zinc-700')
                    ->title('text-sm font-semibold text-zinc-800 dark:text-zinc-200')
                    ->close('rounded-lg p-1.5 text-zinc-500 transition-colors hover:bg-zinc-100 dark:text-zinc-400 dark:hover:bg-zinc-700')
                    ->body('flex-1 overflow-y-auto px-4 py-4')
                    ->footer('border-t border-zinc-200 px-4 py-3 dark:border-zinc-700')
                    ->clearAll($this->button().' w-full justify-center')
                )
                ->toArray(),
        ];
    }

    /** @return array<string, mixed> */
    public function resolveTokens(): array
    {
        if (empty($this->tokens)) {
            $tokens = parent::resolveTokens();

            $this->tokens = array_replace_recursive($tokens, [
                'table' => [
                    'body' => [
                        'td' => [
                            'actions_wrapper' => 'flex items-center gap-1 justify-end',
                        ],
                    ],
                ],
                'pagination' => [
                    'wrapper' => 'flex items-center justify-between w-full',
                    'count_wrapper' => 'flex items-center justify-between w-full gap-4',
                    'count_text' => 'text-xs font-medium text-zinc-500 dark:text-zinc-400 whitespace-nowrap',
                    'count_value' => 'font-semibold text-zinc-700 dark:text-zinc-200',
                ],
            ]);
        }

        return $this->tokens;
    }

    private function input(): string
    {
        return 'rounded-lg border border-zinc-200 border-b-zinc-300/80 dark:border-white/10 bg-white dark:bg-white/10 shadow-xs text-zinc-700 dark:text-zinc-300 placeholder-zinc-400 dark:placeholder-zinc-500 text-sm py-2 px-3 focus:outline-none focus:ring-2 focus:ring-zinc-400/30 dark:focus:ring-white/20 transition-colors';
    }

    private function select(): string
    {
        return 'rounded-lg border border-zinc-200 border-b-zinc-300/80 dark:border-white/10 bg-white dark:bg-white/10 shadow-xs text-zinc-700 dark:text-zinc-300 text-sm py-2 px-3 pr-8 focus:outline-none focus:ring-2 focus:ring-zinc-400/30 dark:focus:ring-white/20 transition-colors appearance-none cursor-pointer';
    }

    private function button(): string
    {
        return 'inline-flex items-center gap-1.5 rounded-lg border border-zinc-200 border-b-zinc-300/80 dark:border-zinc-600 bg-white dark:bg-zinc-700 shadow-xs px-3 py-2 text-sm font-medium text-zinc-700 dark:text-white hover:bg-zinc-50 dark:hover:bg-zinc-600/75 focus:outline-none cursor-pointer transition-colors';
    }

    /**
     * Shared square icon-button used by every header control (toggle-columns,
     * soft-deletes, export, filter) so they render identically to the filter trigger.
     */
    private function triggerButton(): string
    {
        return 'relative inline-flex items-center justify-center font-medium whitespace-nowrap !w-12 !h-10 text-sm rounded-lg gap-2 bg-zinc-800/5 hover:bg-zinc-800/10 dark:bg-white/10 dark:hover:bg-white/20 text-zinc-800 dark:text-white transition';
    }
}
