<?php

namespace PowerComponents\LivewirePowerGrid\Tests\Concerns\Components;

use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Models\Dish;
use PowerComponents\LivewirePowerGrid\{Column, Footer, Header, PowerGrid, PowerGridComponent, PowerGridFields};

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

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('name');
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
