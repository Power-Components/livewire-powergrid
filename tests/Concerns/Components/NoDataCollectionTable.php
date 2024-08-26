<?php

namespace PowerComponents\LivewirePowerGrid\Tests\Concerns\Components;

use Illuminate\Support\Collection;
use PowerComponents\LivewirePowerGrid\{Column, Facades\PowerGrid, PowerGridComponent, PowerGridFields};

class NoDataCollectionTable extends PowerGridComponent
{
    public function datasource(): Collection
    {
        return collect([]);
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('name');
    }

    public function columns(): array
    {
        return [
            Column::add()
                ->title(__('ID'))
                ->field('id')
                ->searchable()
                ->sortable(),

            Column::add()
                ->title(__('Name'))
                ->field('name')
                ->searchable()
                ->sortable(),

            Column::action('Action'),
        ];
    }

    public function actions($row): array
    {
        return [];
    }

    public function filters(): array
    {
        return [];
    }

    public function setTestThemeClass(string $themeClass): void
    {
        config(['livewire-powergrid.theme' => $themeClass]);
    }
}
