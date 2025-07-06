<?php

namespace PowerComponents\LivewirePowerGrid\Themes;

class DaisyUI extends Theme
{
    public string $name = 'daisyui';

    public function table(): array
    {
        return [
            'layout' => [
                'base'      => 'p-3 align-middle inline-block min-w-full w-full sm:px-6 lg:px-8',
                'div'       => 'rounded-t-lg relative border-x border-t border-base-300',
                'table'     => 'table table-zebra',
                'container' => '-my-2 overflow-x-auto sm:-mx-3 lg:-mx-8',
                'actions'   => 'gap-2',
            ],

            'header' => [
                'thead'    => 'text-base-content !capitalize',
                'tr'       => 'bg-base-200',
                'th'       => '',
                'thAction' => '',
            ],

            'body' => [
                'tbody'              => '',
                'tbodyEmpty'         => '',
                'tr'                 => '',
                'td'                 => '',
                'tdEmpty'            => '',
                'tdSummarize'        => '',
                'trSummarize'        => '',
                'tdFilters'          => '',
                'trFilters'          => '',
                'tdActionsContainer' => 'flex gap-2',
            ],
        ];
    }

    public function layout(): array
    {
        return [
            'table'      => $this->root() . '.table-base',
            'header'     => $this->root() . '.header',
            'pagination' => $this->root() . '.pagination',
            'footer'     => $this->root() . '.footer',
        ];
    }

    public function footer(): array
    {
        return [
            'view'                   => $this->root() . '.footer',
            'select'                 => 'select flex rounded-md py-1.5 px-4 pr-7 w-auto',
            'footer'                 => 'border-x border-b rounded-b-lg border-b !border-base-200 !text-base-content',
            'footer_with_pagination' => 'md:flex md:flex-row w-full items-center py-3 overflow-y-auto pl-2 pr-2 relative !text-base-content',
        ];
    }

    public function cols(): array
    {
        return [
            'div' => 'select-none flex items-center gap-1 !text-base-content',
        ];
    }

    public function editable(): array
    {
        return [
            'view'  => $this->root() . '.editable',
            'input' => 'input input-sm',
        ];
    }

    public function toggleable(): array
    {
        return [
            'view' => $this->root() . '.toggleable',
        ];
    }

    public function checkbox(): array
    {
        return [
            'th'    => 'px-6 py-3 text-left text-xs font-medium tracking-wider',
            'base'  => '',
            'label' => 'flex items-center space-x-3',
            'input' => 'checkbox checkbox-sm',
        ];
    }

    public function radio(): array
    {
        return [
            'th'    => 'px-6 py-3 text-left text-xs font-medium tracking-wider',
            'base'  => '',
            'label' => 'flex items-center space-x-3',
            'input' => 'radio',
        ];
    }

    public function filterBoolean(): array
    {
        return [
            'view'   => $this->root() . '.filters.boolean',
            'base'   => 'min-w-[5rem]',
            'select' => 'select',
        ];
    }

    public function filterDatePicker(): array
    {
        return [
            'base'  => '',
            'view'  => $this->root() . '.filters.date-picker',
            'input' => 'flatpickr flatpickr-input input',
        ];
    }

    public function filterMultiSelect(): array
    {
        return [
            'view'   => $this->root() . '.filters.multi-select',
            'base'   => 'inline-block relative w-full',
            'select' => 'mt-1',
        ];
    }

    public function filterNumber(): array
    {
        return [
            'view'  => $this->root() . '.filters.number',
            'input' => 'w-full min-w-[5rem] block input',
        ];
    }

    public function filterSelect(): array
    {
        return [
            'view'   => $this->root() . '.filters.select',
            'base'   => '',
            'select' => 'select',
        ];
    }

    public function filterInputText(): array
    {
        return [
            'view'   => $this->root() . '.filters.input-text',
            'base'   => 'min-w-[9.5rem]',
            'select' => 'select',
            'input'  => 'input',
        ];
    }

    public function searchBox(): array
    {
        return [
            'input'      => 'grow',
            'iconClose'  => 'text-base-content',
            'iconSearch' => 'text-base-content grow mr-2 w-5 h-5',
        ];
    }
}
