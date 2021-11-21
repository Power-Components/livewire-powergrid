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

    public string $sortField = 'dishes.id';

    public function datasource(): ?Builder
    {
        return Dish::query()
            ->join('categories', function ($categories) {
                $categories->on('dishes.category_id', '=', 'categories.id');
            })
            ->select([
                'dishes.id',
                'dishes.calories',
                'categories.name',
            ]);
    }

    public function addColumns(): ?PowerGridEloquent
    {
        return PowerGrid::eloquent()
            ->addColumn('dishes.id')
            ->addColumn('calories')
            ->addColumn('category_id', function (Dish $dish) {
                return $dish->category_id;
            })
            ->addColumn('category.name');
    }

    public function columns(): array
    {
        return [
            Column::add()
                ->title(__('ID'))
                ->field('dishes.id')
                ->searchable()
                ->sortable('dishes.id'),

            Column::add()
                ->title(__('Calorias'))
                ->field('calories')
                ->makeInputRange('calories')
                ->sortable(),

            Column::add()
                ->title(__('Categoria'))
                ->field('categories.name')
                ->makeInputMultiSelect(Category::all(), 'name', 'category_id')
                ->sortable('categories.name'),
        ];
    }
}
