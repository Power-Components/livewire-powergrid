<?php

namespace PowerComponents\LivewirePowerGrid\Themes;

class Bootstrap5 extends Theme
{
    public string $name = 'bootstrap5';

    public function table(): array
    {
        return [
            'layout' => [
                'base'      => 'pt-3 px-sm-3 px-lg-5 align-middle d-inline-block',
                'div'       => 'table-responsive col-md-12 my-2 mx-0',
                'table'     => 'table-hover table-striped w-100',
                'container' => 'my-0 mx-sm-n1 mx-lg-n3 overflow-x-auto',
                'actions'   => 'd-flex gap-1',
            ],

            'header' => [
                'thead'    => '',
                'tr'       => '',
                'th'       => 'fw-bold text-secondary text-nowrap small py-2',
                'thAction' => '',
            ],

            'body' => [
                'tbody'              => 'table-group-divider',
                'tbodyEmpty'         => '',
                'tr'                 => '',
                'td'                 => 'align-middle text-nowrap px-3 py-2 lh-sm',
                'tdEmpty'            => 'p-2 text-nowrap',
                'tdSummarize'        => 'text-dark-emphasis small px-3 py-2 lh-sm',
                'trSummarize'        => '',
                'tdFilters'          => '',
                'trFilters'          => '',
                'tdActionsContainer' => 'd-flex gap-1',
            ],
        ];
    }

    public function cols(): array
    {
        return [
            'div' => '',
        ];
    }

    public function footer(): array
    {
        return [
            'view'                   => $this->root() . '.footer',
            'select'                 => '',
            'footer'                 => 'mt-50 pb-1 w-100 align-items-end px-1 d-flex flex-wrap justify-content-sm-center justify-content-md-between',
            'footer_with_pagination' => '',
        ];
    }

    public function toggleable(): array
    {
        return [
            'view'  => $this->root() . '.toggleable',
            'base'  => 'form-check form-switch',
            'label' => 'form-check-label',
            'input' => 'form-check-input',
            'role'  => 'switch',
        ];
    }

    public function editable(): array
    {
        return [
            'view'  => $this->root() . '.editable',
            'input' => 'form-control',
        ];
    }

    public function checkbox(): array
    {
        return [
            'th'    => 'fs-6 text-center',
            'base'  => 'form-check',
            'label' => 'form-check-label',
            'input' => 'form-check-input',
        ];
    }

    public function radio(): array
    {
        return [
            'th'    => '',
            'base'  => 'form-check',
            'label' => 'form-check-label',
            'input' => 'form-check-input',
        ];
    }

    public function filterBoolean(): array
    {
        return [
            'view'   => $this->root() . '.filters.boolean',
            'base'   => '',
            'select' => 'form-control form-select form-select-sm',
        ];
    }

    public function filterDatePicker(): array
    {
        return [
            'base'  => '',
            'view'  => $this->root() . '.filters.date-picker',
            'input' => 'form-control form-control-sm',
        ];
    }

    public function filterMultiSelect(): array
    {
        return [
            'view'   => $this->root() . '.filters.multi-select',
            'base'   => '',
            'select' => 'form-control form-select form-select-sm',
        ];
    }

    public function filterNumber(): array
    {
        return [
            'view'  => $this->root() . '.filters.number',
            'input' => 'form-control form-control-sm text-12',
        ];
    }

    public function filterSelect(): array
    {
        return [
            'view'   => $this->root() . '.filters.select',
            'base'   => '',
            'select' => 'form-control form-select-sm form-select',
        ];
    }

    public function filterInputText(): array
    {
        return [
            'view'   => $this->root() . '.filters.input-text',
            'base'   => '',
            'select' => 'form-control form-select-sm mb-1 form-select',
            'input'  => 'form-control form-control-sm',
        ];
    }

    public function searchBox(): array
    {
        return [
            'input'      => 'col-12 col-sm-8 form-control',
            'iconSearch' => 'bi bi-search',
            'iconClose'  => '',
        ];
    }
}
