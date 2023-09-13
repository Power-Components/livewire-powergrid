<?php

namespace PowerComponents\LivewirePowerGrid\Tests;

use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Tests\Models\Dish;
use PowerComponents\LivewirePowerGrid\{Column,
    Header,
    PowerGrid,
    PowerGridColumns,
    PowerGridComponent};

class DishesTableWithJoinNames extends PowerGridComponent
{
    public function setUp(): array
    {
        return [
            Header::make()
                ->showSearchInput(),
        ];
    }

    public function dataSource(): Builder
    {
        return Dish::query()
            ->join('categories as newCategories', function ($categories) {
                $categories->on('dishes.category_id', '=', 'newCategories.id');
            })
            ->select('dishes.*', 'newCategories.name as category_name');
    }

    public function addColumns(): PowerGridColumns
    {
        return PowerGrid::columns()
            ->addColumn('id')
            ->addColumn('dish_name', fn (Dish $dish) => $dish->name)
            ->addColumn('category_name', fn (Dish $dish) => $dish->category->name);
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id')
                ->searchable()
                ->sortable(),

            Column::make('Dish', 'dish_name', 'dishes.name')
                ->searchable()
                ->sortable(),

            Column::make('Category', 'category_name', 'newCategories.name')
                ->searchable()
                ->sortable(),

            Column::action('Action'),
        ];
    }

    public function bootstrap()
    {
        config(['livewire-powergrid.theme' => 'bootstrap']);
    }

    public function tailwind()
    {
        config(['livewire-powergrid.theme' => 'tailwind']);
    }
}
