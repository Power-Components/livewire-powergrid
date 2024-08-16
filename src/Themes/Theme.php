<?php

namespace PowerComponents\LivewirePowerGrid\Themes;

class Theme
{
    public string $name = '';

    public string $base = 'livewire-powergrid::components.frameworks.';

    public function root(): string
    {
        return $this->base . $this->name;
    }

    public function toggleable(): array
    {
        return [
            'view' => $this->root() . '.toggleable',
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

    public function apply(): array
    {
        return [
            'table'             => $this->table(),
            'footer'            => $this->footer(),
            'cols'              => $this->cols(),
            'editable'          => $this->editable(),
            'layout'            => $this->layout(),
            'toggleable'        => $this->toggleable(),
            'checkbox'          => $this->checkbox(),
            'radio'             => $this->radio(),
            'filterBoolean'     => $this->filterBoolean(),
            'filterDatePicker'  => $this->filterDatePicker(),
            'filterMultiSelect' => $this->filterMultiSelect(),
            'filterNumber'      => $this->filterNumber(),
            'filterSelect'      => $this->filterSelect(),
            'filterInputText'   => $this->filterInputText(),
            'searchBox'         => $this->searchBox(),
        ];
    }
}
