<?php

namespace PowerComponents\LivewirePowerGrid\Tests\Concerns\Components;

use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\{PowerGridComponent, PowerGridFields};
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Models\Dish;
use PowerComponents\Turbine\Column;

class DishesCustomSortTable extends PowerGridComponent
{
    public string $tableName = 'dishes-custom-sort-table';

    public function setUp(): array
    {
        return [
            PowerGrid::header()
                ->showSearchInput(),

            PowerGrid::footer()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return Dish::query();
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('name')
            ->add('price')
            ->add('calories');
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id')
                ->sortable(),

            Column::make('Name', 'name')
                ->sortable(),

            Column::make('Price', 'price')
                ->sortUsing(function ($query, string $direction) {
                    // Custom sort: sort by price, but nulls go last
                    $query->orderByRaw("price IS NULL, price {$direction}");
                }),

            Column::make('Calories', 'calories')
                ->sortUsing(function ($query, string $direction) {
                    // Custom sort: multiply calories by a factor and sort
                    $query->orderByRaw("calories * 2 {$direction}");
                }),

            Column::action('Action'),
        ];
    }

    public function setTestThemeClass(string $themeClass): void
    {
        config(['livewire-powergrid.theme' => $themeClass]);
    }
}
