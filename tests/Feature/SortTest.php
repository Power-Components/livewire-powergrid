<?php

use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\{Column, Facades\PowerGrid, PowerGridComponent, PowerGridFields};

it('properly sorts from collection', function (string $field, array $ascending, array $descending) {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-sort';

        public function datasource()
        {
            return collect([
                ['id' => 1, 'name' => 'Dish A', 'price' => 100.00, 'in_stock' => true, 'produced_at' => '2021-10-01'],
                ['id' => 2, 'name' => 'Dish B', 'price' => 200.10, 'in_stock' => true, 'produced_at' => '2021-10-02'],
                ['id' => 3, 'name' => 'Dish C', 'price' => 300.50, 'in_stock' => false, 'produced_at' => '2021-10-03'],
            ]);
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()
                ->add('id')
                ->add('name')
                ->add('price')
                ->add('in_stock')
                ->add('produced_at');
        }

        public function columns(): array
        {
            return [
                Column::make('Id', 'id')->sortable(),
                Column::make('Name', 'name')->sortable(),
                Column::make('Price', 'price')->sortable(),
                Column::make('Stock', 'in_stock')->sortable(),
                Column::make('Produced At', 'produced_at')->sortable(),
            ];
        }
    };

    Livewire::test($component::class)
        ->call('sortBy', $field)
        ->set('sortDirection', 'asc')
        ->assertSeeInOrder($ascending)
        ->set('sortDirection', 'desc')
        ->assertSeeInOrder($descending);
})->with([
    'sort by id' => [
        'field' => 'id',
        'ascending' => ['Dish A', 'Dish B', 'Dish C'],
        'descending' => ['Dish C', 'Dish B', 'Dish A'],
    ],
    'sort by name' => [
        'field' => 'name',
        'ascending' => ['Dish A', 'Dish B', 'Dish C'],
        'descending' => ['Dish C', 'Dish B', 'Dish A'],
    ],
    'sort by price' => [
        'field' => 'price',
        'ascending' => ['Dish A', 'Dish B', 'Dish C'],
        'descending' => ['Dish C', 'Dish B', 'Dish A'],
    ],
    'sort by stock' => [
        'field' => 'in_stock',
        'ascending' => ['Dish C', 'Dish A', 'Dish B'], // false comes first
        'descending' => ['Dish A', 'Dish B', 'Dish C'], // true comes first
    ],
    'sort by produced_at' => [
        'field' => 'produced_at',
        'ascending' => ['Dish A', 'Dish B', 'Dish C'],
        'descending' => ['Dish C', 'Dish B', 'Dish A'],
    ],
]);
