<?php

namespace PowerComponents\LivewirePowerGrid\Tests\Concerns\Components;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Facades\Rule;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Models\Dish;
use PowerComponents\Turbine\Column;

class DishesTableWithJoin extends BaseDishesTable
{
    public string $tableName = 'testing-dishes-with-join-names-table';

    public ?string $primaryKeyAlias = 'id';

    public string $sortField = 'dishes.id';

    public string $primaryKey = 'dishes.id';

    public function dataSource(): Builder
    {
        return Dish::query()
            ->join('categories', function ($categories) {
                $categories->on('dishes.category_id', '=', 'categories.id');
            })
            ->select('dishes.*', 'categories.name as category_name');
    }

    public function relationSearch(): array
    {
        return [
            'category' => [
                'name',
            ],
        ];
    }

    public function fields(): PowerGridFields
    {
        return parent::fields()
            ->add('dish_name', function ($dish) {
                return $dish->name;
            })
            ->add('created_at')
            ->add('created_at_formatted', function ($dish) {
                return Carbon::parse($dish->category->created_at)->format('d/m/Y');
            });
    }

    public function columns(): array
    {
        return [
            Column::add()
                ->title('ID')
                ->field('id')
                ->searchable()
                ->sortable(),

            Column::add()
                ->title('Dish')
                ->field('dish_name', 'name')
                ->searchable()
                ->placeholder('Dish placeholder')
                ->sortable(),

            Column::add()
                ->title('Serving at')
                ->field('serving_at')
                ->sortable(),

            Column::add()
                ->title('Category')
                ->field('category_name', 'categories.name')
                ->sortable()
                ->placeholder('Category placeholder'),

            Column::add()
                ->title('Price')
                ->field('price_BRL'),

            Column::add()
                ->title('Sales Price')
                ->field('sales_price_BRL'),

            Column::add()
                ->title('Calories')
                ->field('calories')
                ->sortable(),

            Column::add()
                ->title('In Stock')
                ->toggleable(true, 'sim', 'não')
                ->sortable()
                ->field('in_stock'),

            Column::add()
                ->title('Created Categories')
                ->sortable()
                ->field('created_at_formatted', 'categories.created_at'),

            Column::add()
                ->title('Production Date')
                ->field('produced_at_formatted'),

            Column::action('Action'),
        ];
    }

    public function actionRules(): array
    {
        return [
            Rule::button('edit-stock-for-rules')
                ->when(fn ($dish) => $dish->id == 2)
                ->hide(),

            Rule::button('edit-stock-for-rules')
                ->when(fn ($dish) => $dish->id == 4)
                ->slot('cation edit for id 4'),

            Rule::rows()
                ->when(fn ($dish) => (bool) $dish->in_stock === false)
                ->setAttribute('class', 'bg-red-100 text-red-800'),

            Rule::rows()
                ->when(fn ($dish) => $dish->id == 3)
                ->setAttribute('class', 'bg-pg-secondary-100'),

            Rule::button('edit-stock-for-rules')
                ->when(fn ($dish) => $dish->id == 5)
                ->dispatch('toggleEvent', ['dishId' => 'id']),

            Rule::button('edit-stock-for-rules')
                ->when(fn ($dish) => $dish->id == 9)
                ->disable(),
        ];
    }
}
