<?php

namespace PowerComponents\LivewirePowerGrid\Themes;

class DaisyUI extends Theme
{
    public function struct(): array
    {
        return Components\ThemeBuilder::make($this->name())
            ->baseView('livewire-powergrid::components.frameworks.daisyui')
            ->header(fn (Components\Header $header) => $header
                ->view('header')
                ->layout(fn (Components\Layout $layout) => $layout
                    ->container('mb-3 md:flex md:flex-row w-full justify-between items-center')
                    ->subContainer('md:flex md:flex-row w-full gap-1')
                    ->actionsContainer('flex flex-row items-center text-sm flex-wrap gap-2')
                    ->actions('btn btn-ghost btn-sm border-base-300')
                )
                ->searchBox(fn (Components\Component $searchBox) => $searchBox
                    ->view('header.search')
                    ->container('w-full md:w-auto mt-2 md:mt-0')
                    ->input('input input-bordered input-sm w-full md:w-80')
                    ->iconClose('text-base-content/50')
                    ->iconSearch('text-base-content/50 h-4 w-4')
                )
            )
            ->table(fn (Components\Table $table) => $table
                ->layout(fn (Components\Layout $layout) => $layout
                    ->container('rounded-t-lg relative border-x border-t border-base-300')
                    ->table('table table-zebra')
                    ->thead('text-base-content !capitalize')
                    ->tr('bg-base-200')
                    ->th('')
                    ->tbody('')
                    ->td('')
                )
                ->checkbox(fn (Components\Component $checkbox) => $checkbox
                    ->th('px-6 py-3 text-left text-xs font-medium tracking-wider')
                    ->input('checkbox checkbox-sm')
                )
                ->radio(fn (Components\Component $radio) => $radio
                    ->th('px-6 py-3 text-left text-xs font-medium tracking-wider')
                    ->input('radio')
                )
            )
            ->footer(fn (Components\Footer $footer) => $footer
                ->layout(fn (Components\Layout $layout) => $layout
                    ->container('p-4 flex flex-col md:flex-row md:items-center justify-between gap-4 border-t border-base-200')
                    ->select('select select-bordered select-sm pr-7 w-auto')
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
                ->input('input input-bordered input-sm w-full')
                ->error('text-sm text-error p-1 transition-all duration-200')
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
            'filter' => (new Components\Filter())
                ->label('block text-sm font-semibold !text-base-content opacity-80')
                ->boolean(fn (Components\Component $boolean) => $boolean
                    ->view('livewire-powergrid::components.frameworks.tailwind.filters.boolean')
                    ->select('select select-sm select-bordered w-full')
                )
                ->datePicker(fn (Components\Component $datePicker) => $datePicker
                    ->view('livewire-powergrid::components.frameworks.tailwind.filters.date-picker')
                    ->input('flatpickr flatpickr-input input input-sm input-bordered w-full')
                )
                ->multiSelect(fn (Components\Component $multiSelect) => $multiSelect
                    ->view('livewire-powergrid::components.frameworks.tailwind.filters.multi-select')
                    ->select('select select-sm select-bordered w-full mt-1')
                )
                ->number(fn (Components\Component $number) => $number
                    ->view('livewire-powergrid::components.frameworks.tailwind.filters.number')
                    ->input('w-full min-w-[5rem] block input input-sm input-bordered')
                )
                ->select(fn (Components\Component $select) => $select
                    ->view('livewire-powergrid::components.frameworks.tailwind.filters.select')
                    ->select('select select-sm select-bordered w-full')
                )
                ->inputText(fn (Components\Component $inputText) => $inputText
                    ->view('livewire-powergrid::components.frameworks.tailwind.filters.input-text')
                    ->select('select select-sm select-bordered w-full')
                    ->input('input input-sm input-bordered w-full')
                )
                ->input('input input-sm')
                ->toArray(),
        ];
    }
}
