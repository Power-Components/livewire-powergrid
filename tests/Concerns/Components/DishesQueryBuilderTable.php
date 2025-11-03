<?php

namespace PowerComponents\LivewirePowerGrid\Tests\Concerns\Components;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use PowerComponents\LivewirePowerGrid\{Button, Column};

class DishesQueryBuilderTable extends BaseDishesTable
{
    public string $tableName = 'testing-dishes-query-builder-table';

    public string $primaryKey = 'dishes.id';

    public function datasource(): Builder
    {
        return DB::table('dishes')
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

    public function columns(): array
    {
        return [
            Column::add()
                ->title('ID')
                ->field('id')
                ->searchable()
                ->sortable(),

            Column::add()
                ->title('Stored at')
                ->field('storage_room')
                ->sortable(),

            Column::add()
                ->title('Dish')
                ->field('name')
                ->searchable()
                ->placeholder('Dish placeholder')
                ->sortable(),

            Column::add()
                ->title('Serving at')
                ->field('serving_at')
                ->sortable(),

            Column::add()
                ->title('Chef')
                ->field('chef_name')
                ->searchable()
                ->placeholder('Chef placeholder')
                ->sortable(),

            Column::add()
                ->title('Category')
                ->field('category_name')
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
                ->field('in_stock'),

            Column::add()
                ->title('Produced At')
                ->field('produced_at_formatted'),

            Column::add()
                ->title('Date')
                ->field('produced_at')
                ->sortable(),

            Column::action('Action'),
        ];
    }

    public function actions($row): array
    {
        return [
            Button::add('edit-stock')
                ->slot('<div id="edit">Edit</div>')
                ->class('text-center')
                ->openModal('edit-stock', ['dishId' => $row->id]),

            Button::add('destroy')
                ->slot('Delete')
                ->class('text-center')
                ->dispatch('deletedEvent', ['dishId' => $row->id]),
        ];
    }

    protected function getCategoryName($dish): string
    {
        return $dish->category_name ?? '';
    }
}
