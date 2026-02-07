<?php

namespace PowerComponents\LivewirePowerGrid\Tests\Concerns\Components;

use Illuminate\Support\Collection;
use PowerComponents\LivewirePowerGrid\{Column, Facades\PowerGrid, PowerGridComponent, PowerGridFields};

class DishesCustomSortCollectionTable extends PowerGridComponent
{
    public string $tableName = 'dishes-custom-sort-collection-table';

    public function datasource(): Collection
    {
        return collect([
            ['id' => 1, 'name' => 'Zebra Dish', 'price' => 100.00, 'calories' => 300],
            ['id' => 2, 'name' => 'Apple Pie', 'price' => 50.00, 'calories' => 200],
            ['id' => 3, 'name' => 'Banana Split', 'price' => 75.00, 'calories' => 400],
            ['id' => 4, 'name' => 'Cherry Tart', 'price' => 25.00, 'calories' => 100],
            ['id' => 5, 'name' => 'Donut', 'price' => 10.00, 'calories' => 500],
        ]);
    }

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
                ->sortUsing(function (Collection $collection, string $direction) {
                    // Custom sort: sort by price with rounding to nearest 50
                    return $collection->sortBy(function ($item) {
                        return round(data_get($item, 'price') / 50) * 50;
                    }, SORT_REGULAR, $direction === 'desc');
                }),

            Column::make('Calories', 'calories')
                ->sortUsing(function (Collection $collection, string $direction) {
                    // Custom sort: sort by calories in groups (low/medium/high)
                    return $collection->sortBy(function ($item) {
                        $calories = data_get($item, 'calories');
                        if ($calories <= 200) {
                            return 1; // Low
                        }
                        if ($calories <= 400) {
                            return 2; // Medium
                        }

                        return 3; // High
                    }, SORT_REGULAR, $direction === 'desc');
                }),

            Column::action('Action'),
        ];
    }

    public function setTestThemeClass(string $themeClass): void
    {
        config(['livewire-powergrid.theme' => $themeClass]);
    }
}
