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
                ->outsideFilters('')
            )
            ->header(fn (Components\Header $header) => $header
                ->view('livewire-powergrid::components.themes.flux.header')
                ->layout(fn (Components\Layout $layout) => $layout
                    ->container('mb-3 md:flex md:flex-row w-full justify-between items-center')
                    ->subContainer('md:flex md:flex-row w-full gap-1.5')
                    ->actionsContainer('flex flex-row items-center text-sm flex-wrap gap-2')
                    ->actions($this->button())
                )
                ->searchBox(fn (Components\SearchBox $searchBox) => $searchBox
                    ->view('livewire-powergrid::components.themes.flux.header.search')
                    ->container('flex flex-row mt-2 md:mt-0 w-full justify-start sm:justify-center md:justify-end')
                    ->relativeMain('w-full md:w-4/12 lg:w-1/2')
                )
            )
            ->table(fn (Components\Table $table) => $table
                ->layout(fn (Components\Layout $layout) => $layout
                    ->container('overflow-x-auto rounded-t-lg relative border border-zinc-200 dark:border-zinc-700 dark:bg-zinc-900')
                    ->table('min-w-full')
                    ->thead('bg-white dark:bg-white/10')
                    ->tr('border-b border-zinc-100 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-800/60')
                    ->th('font-semibold px-3 py-3 text-left text-xs text-zinc-500 tracking-wider whitespace-nowrap dark:text-zinc-400')
                    ->thActions('font-semibold px-3 py-3 text-end text-xs text-zinc-500 tracking-wider whitespace-nowrap dark:text-zinc-400')
                    ->tbody('text-zinc-800 dark:text-zinc-200')
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
                    ->container('flex flex-wrap items-center gap-2 overflow-hidden rounded-b-lg border-x border-b border-zinc-200 dark:border-zinc-700 dark:bg-zinc-900 px-4 py-3')
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
                ->view('livewire-powergrid::components.themes.tailwind.toggleable')
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
                    ->input('flatpickr flatpickr-input '.$this->input().' w-auto')
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
}
