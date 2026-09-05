<?php

namespace PowerComponents\LivewirePowerGrid\Themes;

class DaisyUI extends Theme
{
    protected ?string $parentTheme = Tailwind::class;

    protected function baseView(): string
    {
        return 'livewire-powergrid::components.themes.daisyui';
    }

    public function struct(): Components\ThemeBuilder
    {
        return Components\ThemeBuilder::make($this->name())
            ->baseView($this->baseView());
    }

    /** @return array<string, mixed> */
    public function layout(): array
    {
        return $this->section('layout', fn (Components\Layout $layout) => $layout
            ->wrapper('space-y-4 text-sm')
            ->card('rounded-lg border border-base-300 bg-base-100 overflow-visible text-sm')
            ->outsideFilters('')
        );
    }

    /** @return array<string, mixed> */
    public function header(): array
    {
        $header = $this->section('header', fn (Components\Header $header) => $header
            ->view('header')
            ->layout(fn (Components\Layout $layout) => $layout
                ->container('p-4 md:flex md:flex-row w-full justify-between items-center gap-3')
                ->subContainer('flex flex-row flex-wrap items-center gap-1')
                ->actionsContainer('flex flex-row items-center text-sm flex-wrap gap-2')
                ->actions('btn btn-ghost btn-sm border-base-300')
            )
            ->searchBox(fn (Components\SearchBox $searchBox) => $searchBox
                ->view('header.search')
                ->container('w-full')
                ->relativeMain('input input-bordered input-sm flex items-center gap-2 w-full')
                ->input('grow border-0 bg-transparent px-0 focus:outline-none focus:ring-0')
                ->iconSearchWrapper('')
                ->iconSearch('text-base-content opacity-50 h-4 w-4')
                ->iconCloseWrapper('')
                ->iconClose('text-base-content opacity-50')
                ->icon('livewire-powergrid::icons.search')
                ->iconClear('livewire-powergrid::icons.x')
            )
            ->toggleColumns(fn (Components\HeaderButton $button) => $button
                ->button('btn btn-ghost btn-sm border-base-300')
                ->iconClass('w-5 h-5 shrink-0')
                ->label('ml-2')
                ->menu('menu p-2 shadow bg-base-100 rounded-box w-52 mt-2 text-sm absolute z-10')
                ->menuItem('text-sm text-base-content')
                ->panel('absolute left-0 top-full z-50 mt-2 flex max-h-[calc(100dvh-8rem)] w-56 flex-col overflow-hidden rounded-box border border-base-300 bg-base-100 shadow-lg')
            )
            ->softDeletes(fn (Components\HeaderButton $button) => $button
                ->button('btn btn-ghost btn-sm border-base-300')
                ->iconClass('w-5 h-5 shrink-0')
                ->label('ml-2')
                ->menu('menu p-2 shadow bg-base-100 rounded-box w-52 mt-2 text-sm absolute z-10')
                ->menuItem('text-sm')
            )
            ->filters(fn (Components\HeaderButton $button) => $button
                ->wrapper('flex mt-2 sm:mt-0 gap-3')
                ->button('btn btn-ghost btn-sm border-base-300')
                ->iconClass('w-5 h-5 shrink-0')
                ->label('ml-2')
            )
            ->filterBuilder(fn (Components\HeaderButton $button) => $button
                ->button('btn btn-ghost btn-sm border-base-300')
                ->iconClass('w-5 h-5 shrink-0')
                ->label('ml-2')
                ->badge('badge badge-primary badge-sm ml-1')
            )
            ->enabledFilters(fn (Components\HeaderButton $button) => $button
                ->wrapper('flex group items-center cursor-pointer')
                ->label('text-xs font-medium text-base-content/60')
                ->iconClass('w-3.5 h-3.5')
                ->pill('badge badge-outline badge-primary gap-1 hover:bg-base-200 transition-colors')
                ->pillClearAll('')
            )
            ->export(fn (Components\HeaderButton $button) => $button
                ->button('btn btn-ghost btn-sm border-base-300')
                ->iconClass('w-5 h-5 shrink-0')
                ->label('ml-2')
                ->menu('menu p-2 shadow bg-base-100 rounded-box w-52 mt-2 text-sm absolute z-10')
                ->menuItem('text-sm')
            )
        );

        return array_replace_recursive($header, [
            'header' => ['toggle_columns' => ['item_label' => 'text-sm text-base-content']],
        ]);
    }

    /** @return array<string, mixed> */
    public function table(): array
    {
        return $this->section('table', fn (Components\Table $table) => $table
            ->layout(fn (Components\Layout $layout) => $layout
                ->container('overflow-x-auto')
                ->table('table table-zebra')
                ->thead('')
                ->tr('hover:bg-base-300')
                ->theadTr('hover:bg-transparent')
                ->emptyState('px-6 py-12 text-center text-sm text-base-content/60')
                ->trStriped('')
                ->trNotStriped('')
                ->th('')
                ->thActions('text-end')
                ->tbody('')
                ->td('')
                ->tdActions('text-end')
            )
            ->body(fn (Components\Body $body) => $body
                ->tr(fn (Components\Tr $tr) => $tr
                    ->responsive('text-base-content')
                    ->responsiveToggleIcon('text-base-content')
                )
            )
            ->checkbox(fn (Components\Checkbox $checkbox) => $checkbox
                ->th('')
                ->base('')
                ->label('')
                ->input('checkbox')
            )
            ->radio(fn (Components\Radio $radio) => $radio
                ->th('')
                ->base('')
                ->label('')
                ->input('radio')
            )
        );
    }

    /** @return array<string, mixed> */
    public function footer(): array
    {
        $footer = $this->section('footer', fn (Components\Footer $footer) => $footer
            ->layout(fn (Components\Layout $layout) => $layout
                ->container('p-4 flex flex-col md:flex-row md:items-center justify-between gap-4 rounded-b-lg border-t border-base-200')
                ->select('select select-bordered select-sm pr-7 w-auto')
            )
            ->pagination('pagination')
        );

        return array_replace_recursive($footer, [
            'pagination' => [
                'count_wrapper' => 'items-center text-sm justify-between w-full sm:flex-1 sm:flex',
                'records_text' => 'leading-5 text-center sm:text-right',
                'nav_wrapper' => 'flex join justify-center mt-2 md:flex-none md:justify-end sm:mt-0',
                'item_first' => 'btn btn-sm join-item',
                'item_prev' => 'btn btn-sm join-item flex items-center',
                'item_active' => 'btn btn-sm join-item btn-primary',
                'item' => 'btn btn-sm join-item',
                'item_next' => 'btn btn-sm join-item flex',
                'item_last' => 'btn btn-sm join-item',
                'button_disabled' => 'btn btn-sm join-item',
                'button_cursor' => 'btn btn-sm join-item',
                'button' => 'select-none btn btn-sm join-item',
            ],
        ]);
    }

    /** @return array<string, mixed> */
    public function tabs(): array
    {
        return $this->section('tabs', fn (Components\Tabs $tabs) => $tabs
            ->list('tabs tabs-box')
            ->tab('tab gap-2')
            ->tabActive('tab-active')
            ->tabInactive('')
            ->badge('badge badge-sm')
            ->badgeActive('badge-primary')
            ->badgeInactive('badge-ghost')
        );
    }

    /** @return array<string, mixed> */
    public function editable(): array
    {
        return [
            'editable' => (new Components\Component())
                ->input('input input-bordered input-sm w-full')
                ->error('text-sm text-error p-1 transition-all duration-200')
                ->toArray(),
        ];
    }

    /** @return array<string, mixed> */
    public function toggleable(): array
    {
        return [
            'toggleable' => (new Components\Component())
                ->fill([
                    'colorOn' => 'var(--color-primary, oklch(0.45 0.24 277))',
                    'colorOff' => 'var(--color-base-300, #d1d5db)',
                    'colorOnDark' => 'var(--color-primary, oklch(0.45 0.24 277))',
                    'colorOffDark' => 'var(--color-base-300, #d1d5db)',
                    'knobOn' => 'var(--color-primary-content, #ffffff)',
                ])
                ->toArray(),
        ];
    }

    /** @return array<string, mixed> */
    public function filter(): array
    {
        return [
            'filter' => (new Components\Filter())
                ->label('block text-sm font-semibold !text-base-content opacity-80')
                ->boolean(fn (Components\Component $boolean) => $boolean
                    ->view('livewire-powergrid::components.themes.tailwind.filters.boolean')
                    ->select('select select-sm select-bordered w-full')
                )
                ->datePicker(fn (Components\Component $datePicker) => $datePicker
                    ->view('powergrid-plugins::Flatpickr.index')
                    ->input('flatpickr flatpickr-input input input-sm input-bordered w-full')
                )
                ->multiSelect(fn (Components\Component $multiSelect) => $multiSelect
                    ->select('select select-sm select-bordered w-full mt-1')
                )
                ->number(fn (Components\Component $number) => $number
                    ->view('livewire-powergrid::components.themes.tailwind.filters.number')
                    ->input('w-full min-w-[5rem] block input input-sm input-bordered')
                )
                ->select(fn (Components\Component $select) => $select
                    ->view('livewire-powergrid::components.themes.tailwind.filters.select')
                    ->select('select select-sm select-bordered w-full')
                )
                ->inputText(fn (Components\Component $inputText) => $inputText
                    ->view('livewire-powergrid::components.themes.tailwind.filters.input-text')
                    ->select('select select-sm select-bordered w-full')
                    ->input('input input-sm input-bordered w-full')
                )
                ->input('input input-sm')
                ->dropdown(fn (Components\Dropdown $dropdown) => $dropdown
                    ->view('livewire-powergrid::components.themes.tailwind.filter')
                    ->wrapper('relative inline-block text-left')
                    ->trigger('btn btn-ghost btn-sm border-base-300 relative gap-2 justify-center')
                    ->badge('absolute -top-1.5 -right-1.5 badge badge-primary badge-sm')
                    ->panel('fixed inset-x-4 top-4 z-50 flex max-h-[calc(100dvh-2rem)] max-w-[calc(100vw-2rem)] flex-col overflow-hidden origin-top rounded-box border border-base-300 bg-base-100 shadow-lg')
                    ->header('flex shrink-0 items-center justify-between px-4 pt-4 pb-2')
                    ->title('text-base font-semibold text-base-content')
                    ->body('min-h-0 flex-1 overflow-y-auto px-4 py-3')
                    ->grid('grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4')
                    ->footer('flex shrink-0 items-center justify-end gap-2 border-t border-base-300 px-4 py-3')
                    ->reset('btn btn-ghost btn-sm text-error')
                    ->clear('btn btn-outline btn-sm')
                    ->apply('btn btn-primary btn-sm')
                )
                ->flyout(fn (Components\Flyout $flyout) => $flyout
                    ->overlay('fixed inset-0 z-40 bg-neutral/40')
                    ->panel('fixed inset-y-0 z-50 flex w-full max-w-full flex-col bg-base-100 shadow-xl sm:w-96 sm:max-w-[90vw]')
                    ->panelLeft('left-0 border-r border-base-300')
                    ->panelRight('right-0 border-l border-base-300')
                    ->header('flex items-center justify-between gap-3 border-b border-base-300 px-4 py-3')
                    ->title('font-semibold text-base-content')
                    ->close('btn btn-ghost btn-sm btn-square')
                    ->body('flex-1 overflow-y-auto px-4 py-4')
                    ->footer('border-t border-base-300 px-4 py-3')
                    ->clearAll('btn btn-outline btn-sm w-full')
                )
                ->toArray(),
        ];
    }
}
