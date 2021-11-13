<?php

namespace PowerComponents\LivewirePowerGrid\Tests;

use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Tests\Models\{Category, Dish};
use PowerComponents\LivewirePowerGrid\{
    Column,
    PowerGrid,
    PowerGridComponent,
    PowerGridEloquent,
    Traits\ActionButton};

class DishesTableWithJoin extends PowerGridComponent
{
    use ActionButton;

    public function setUp()
    {
        $this->sortBy('dishes.id');
    }

    public function datasource(): ?Builder
    {
        return Dish::query()
            ->join('categories', function ($categories) {
                $categories->on('dishes.category_id', '=', 'categories.id');
            })
            ->select([
                'dishes.id as dishes_id',
                'dishes.calories',
                'categories.name as category_name',
            ]);
    }

    public function addColumns(): ?PowerGridEloquent
    {
        return PowerGrid::eloquent()
            ->addColumn('dishes_id')
            ->addColumn('calories')
            ->addColumn('category_id', function (Dish $dish) {
                return $dish->category_id;
            })
            ->addColumn('category_name');
    }

    public function columns(): array
    {
        return [
            Column::add()
                ->title(__('ID'))
                ->field('dishes_id')
                ->searchable()
                ->sortable('dishes_id'),

            Column::add()
                ->title(__('Calorias'))
                ->field('calories')
                ->makeInputRange('calories')
                ->sortable(),

            Column::add()
                ->title(__('Categoria'))
                ->field('category_name')
                ->makeInputMultiSelect(Category::all(), 'name', 'category_id')
                ->sortable('categories.name'),
        ];
    }
}
