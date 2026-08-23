<?php

namespace PowerComponents\LivewirePowerGrid\Themes;

class DaisyUI extends Theme
{
    protected ?string $parentTheme = Tailwind::class;

    public function struct(): Components\ThemeBuilder
    {
        return Components\ThemeBuilder::make($this->name())
            ->baseView('livewire-powergrid::components.themes.daisyui')
            ->layout(fn (Components\Layout $layout) => $layout
                ->wrapper('space-y-4 text-sm')
                ->card('rounded-lg border border-base-300 bg-base-100 overflow-visible text-sm')
                ->outsideFilters('')
            )
            ->header(fn (Components\Header $header) => $header
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
                    ->menuItem('text-sm')
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
            )
            ->table(fn (Components\Table $table) => $table
                ->layout(fn (Components\Layout $layout) => $layout
                    ->container('overflow-x-auto relative border-t border-base-300')
                    ->table('table table-zebra')
                    ->thead('')
                    ->tr('')
                    ->trStriped('bg-base-200')
                    ->trNotStriped('bg-base-100')
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
                    ->input('checkbox checkbox-sm')
                )
                ->radio(fn (Components\Radio $radio) => $radio
                    ->th('')
                    ->input('radio radio-sm')
                )
            )
            ->footer(fn (Components\Footer $footer) => $footer
                ->layout(fn (Components\Layout $layout) => $layout
                    ->container('p-4 flex flex-col md:flex-row md:items-center justify-between gap-4 rounded-b-lg border-t border-base-200')
                    ->select('select select-bordered select-sm pr-7 w-auto')
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
                    ->view('livewire-powergrid::components.themes.tailwind.filters.multi-select')
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
                    ->panel('absolute left-0 sm:left-auto sm:right-0 z-40 mt-2 w-[90vw] origin-top rounded-box border border-base-300 bg-base-100 shadow-lg')
                    ->header('flex items-center justify-between px-4 pt-4 pb-2')
                    ->title('text-base font-semibold text-base-content')
                    ->body('px-4 py-3 max-h-96 overflow-y-auto')
                    ->grid('grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4')
                    ->footer('flex items-center justify-end gap-2 border-t border-base-300 px-4 py-3')
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
