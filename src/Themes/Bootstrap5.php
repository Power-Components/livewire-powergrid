<?php

namespace PowerComponents\LivewirePowerGrid\Themes;

class Bootstrap5 extends Theme
{
    public string $name = 'bootstrap5';

    public function table(): array
    {
        return [
            'layout' => [
                'base'      => '',
                'div'       => ['table-responsive col-md-12', 'margin: 10px 0 10px;'],
                'table'     => '',
                'container' => '',
                'actions'   => 'd-flex gap-2',
            ],

            'header' => [
                'thead'    => '',
                'tr'       => '',
                'th'       => ['', 'white-space: nowrap; min-width: 50px; font-size: 0.75rem; color: #6b6a6a; padding-top: 8px; padding-bottom: 8px;'],
                'thAction' => '',
            ],

            'body' => [
                'tbody'              => '',
                'tbodyEmpty'         => '',
                'tr'                 => 'border-b border-pg-primary-100 dark:border-pg-primary-600 hover:bg-pg-primary-50 dark:bg-pg-primary-800 dark:hover:bg-pg-primary-700',
                'td'                 => ['', 'vertical-align: middle; line-height: normal; white-space: nowrap;'],
                'tdEmpty'            => '',
                'tdSummarize'        => ['', 'font-size: 0.875rem; line-height: 1.25rem; --tw-text-opacity: 1; color: rgb(76 79 82 / var(--tw-text-opacity)); padding-left: 0.75rem; padding-right: 0.75rem; padding-top: 0.5rem; padding-bottom: 0.5rem;'],
                'trSummarize'        => '',
                'tdFilters'          => '',
                'trFilters'          => '',
                'tdActionsContainer' => 'd-flex gap-2',
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
            'view'   => $this->root() . '.footer',
            'select' => '',
        ];
    }

    public function toggleable(): array
    {
        return [
            'view' => $this->root() . '.toggleable',
        ];
    }

    public function editable(): array
    {
        return [
            'view'  => $this->root() . '.editable',
            'input' => 'form-control shadow-none',
        ];
    }

    public function checkbox(): array
    {
        return [
            'th'    => ['', 'font-size: 1rem !important;text-align:center'],
            'base'  => 'form-check',
            'label' => 'form-check-label',
            'input' => 'form-check-input shadow-none',
        ];
    }

    public function radio(): array
    {
        return [
            'th'    => '',
            'base'  => '',
            'label' => 'form-check-label',
            'input' => 'form-check-input',
        ];
    }

    public function filterBoolean(): array
    {
        return [
            'view'   => $this->root() . '.filters.boolean',
            'base'   => '',
            'select' => 'form-control form-select shadow-none',
        ];
    }

    public function filterDatePicker(): array
    {
        return [
            'base'  => '',
            'view'  => $this->root() . '.filters.date-picker',
            'input' => 'form-control shadow-none',
        ];
    }

    public function filterMultiSelect(): array
    {
        return [
            'view'   => $this->root() . '.filters.multi-select',
            'base'   => '',
            'select' => '',
        ];
    }

    public function filterNumber(): array
    {
        return [
            'view'  => $this->root() . '.filters.number',
            'input' => 'form-control shadow-none',
        ];
    }

    public function filterSelect(): array
    {
        return [
            'view'   => $this->root() . '.filters.select',
            'base'   => '',
            'select' => 'form-control form-select shadow-none',
        ];
    }

    public function filterInputText(): array
    {
        return [
            'view'   => $this->root() . '.filters.input-text',
            'base'   => ['', 'min-width: 165px !important'],
            'select' => 'form-control mb-1 shadow-none form-select',
            'input'  => 'form-control shadow-none',
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
