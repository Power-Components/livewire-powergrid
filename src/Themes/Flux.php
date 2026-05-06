<?php

namespace PowerComponents\LivewirePowerGrid\Themes;

class Flux extends Theme
{
    public function struct(): array
    {
        return Components\ThemeBuilder::make($this->name())
            ->baseView('livewire-powergrid::components.frameworks.flux')
            ->header(fn (Components\Header $header) => $header
                ->view('livewire-powergrid::components.frameworks.tailwind.header')
                ->layout(fn (Components\Layout $layout) => $layout
                    ->subContainer('md:flex md:flex-row w-full gap-1.5')
                    ->actionsContainer('flex flex-row items-center text-sm flex-wrap gap-2')
                    ->actions($this->button())
                )
                ->searchBox(fn (Components\Component $searchBox) => $searchBox
                    ->relativeMain('group relative rounded-full w-full md:w-4/12 float-end float-right md:w-full lg:w-1/2 flex items-center')
                    ->input($this->input().' w-full pl-10 pr-3 py-2')
                    ->iconClose('text-zinc-400 dark:text-zinc-500')
                    ->iconSearch('text-zinc-400 dark:text-zinc-400 w-4 h-4')
                )
            )
            ->table(fn (Components\Table $table) => $table
                ->layout(fn (Components\Layout $layout) => $layout
                    ->container('rounded-t-lg relative border border-zinc-200 dark:border-zinc-700 dark:bg-zinc-900')
                    ->table('min-w-full')
                    ->thead('bg-zinc-50 dark:bg-zinc-800')
                    ->tr('border-b border-zinc-100 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-800/60')
                    ->th('font-semibold px-3 py-3 text-left text-xs text-zinc-500 tracking-wider whitespace-nowrap dark:text-zinc-400')
                    ->tbody('text-zinc-800 dark:text-zinc-200')
                    ->td('px-3 py-2 whitespace-nowrap')
                )
                ->checkbox(fn (Components\Component $checkbox) => $checkbox
                    ->th('px-6 py-3 text-left text-xs font-medium text-zinc-500 tracking-wider')
                    ->input('rounded border-zinc-200 dark:border-white/10 bg-white dark:bg-white/10 h-4 w-4 text-zinc-800 focus:ring-zinc-500')
                )
                ->radio(fn (Components\Component $radio) => $radio
                    ->th('px-6 py-3 text-left text-xs font-medium text-zinc-500 tracking-wider')
                    ->input('rounded-full border-zinc-200 dark:border-white/10 text-zinc-800 focus:ring-zinc-500')
                )
            )
            ->footer(fn (Components\Footer $footer) => $footer
                ->view('livewire-powergrid::components.frameworks.tailwind.footer') // reutiliza o footer do Tailwind
                ->layout(fn (Components\Layout $layout) => $layout
                    ->container('border border-t-0 rounded-b-lg border-zinc-200 dark:border-zinc-700 dark:bg-zinc-900')
                    ->select('shadow-xs rounded-lg border border-zinc-200 border-b-zinc-300/80 dark:border-white/10 bg-white dark:bg-white/10 text-zinc-700 dark:text-zinc-300 text-sm py-2 px-3 pr-8 focus:outline-none')
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
                ->input($this->input().' w-full')
                ->error('text-sm text-red-500 p-1 transition-all duration-200')
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
                ->label('block text-xs font-medium text-zinc-500 dark:text-zinc-400')
                ->boolean(fn (Components\Component $boolean) => $boolean
                    ->view('livewire-powergrid::components.frameworks.tailwind.filters.boolean')
                    ->select($this->input().' w-full pr-8')
                )
                ->datePicker(fn (Components\Component $datePicker) => $datePicker
                    ->view('livewire-powergrid::components.frameworks.tailwind.filters.date-picker')
                    ->input('flatpickr flatpickr-input '.$this->input().' w-auto')
                )
                ->number(fn (Components\Component $number) => $number
                    ->view('livewire-powergrid::components.frameworks.tailwind.filters.number')
                    ->input($this->input().' w-full min-w-[5rem] block')
                )
                ->select(fn (Components\Component $select) => $select
                    ->view('livewire-powergrid::components.frameworks.tailwind.filters.select')
                    ->select($this->input().' w-full pr-8')
                )
                ->inputText(fn (Components\Component $inputText) => $inputText
                    ->view('livewire-powergrid::components.frameworks.tailwind.filters.input-text')
                    ->select($this->input().' w-full pr-8')
                    ->input($this->input().' w-full')
                )
                ->input($this->input().' w-full')
                ->toArray(),
        ];
    }

    private function input(): string
    {
        return 'shadow-xs rounded-lg border border-zinc-200 border-b-zinc-300/80 dark:border-white/10 bg-white dark:bg-white/10 text-zinc-700 dark:text-zinc-300 placeholder-zinc-400 dark:placeholder-zinc-400 text-sm py-2 px-3 focus:outline-none';
    }

    private function button(): string
    {
        return 'inline-flex items-center gap-2 rounded-lg border border-zinc-200 border-b-zinc-300/80 dark:border-zinc-600 bg-white dark:bg-zinc-700 shadow-xs px-3 py-2 text-sm font-medium text-zinc-800 dark:text-white hover:bg-zinc-50 dark:hover:bg-zinc-600/75 focus:outline-none cursor-pointer transition-colors';
    }
}
