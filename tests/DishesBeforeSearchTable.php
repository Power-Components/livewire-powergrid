<?php

namespace PowerComponents\LivewirePowerGrid\Tests;

use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Tests\Models\Dish;
use PowerComponents\LivewirePowerGrid\{
    Column,
    Footer,
    Header,
    PowerGrid,
    PowerGridColumns,
    PowerGridComponent
};

class DishesBeforeSearchTable extends PowerGridComponent
{
    public function setUp(): array
    {
        return [
            Header::make()
                ->showSearchInput(),

            Footer::make()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function beforeSearchName(string $search): string
    {
        return 'Peixada';
    }

    public function datasource(): Builder
    {
        return Dish::query();
    }

    public function addColumns(): PowerGridColumns
    {
        return PowerGrid::columns()
            ->addColumn('id')
            ->addColumn('name');
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id')
                ->searchable()
                ->sortable(),

            Column::make('Dish', 'name', 'dishes.name')
                ->searchable()
                ->sortable(),

            Column::action('Action'),
        ];
    }

    public function bootstrap(): void
    {
        config(['livewire-powergrid.theme' => 'bootstrap']);
    }

    public function tailwind(): void
    {
        config(['livewire-powergrid.theme' => 'tailwind']);
    }
}
