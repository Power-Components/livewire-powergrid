<?php

use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\{Column, PowerGridComponent, PowerGridFields};
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;

it('searches data using nested relations in collection', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-nested-relation';

        public function datasource()
        {
            return collect([
                [
                    'id' => 1,
                    'name' => 'Pastel de Nata',
                    'category' => [
                        'name' => 'Sobremesas',
                        'restaurant' => ['name' => 'Not McDonalds'],
                    ],
                ],
                [
                    'id' => 2,
                    'name' => 'Borsch',
                    'category' => [
                        'name' => 'Sopas',
                        'restaurant' => ['name' => 'Sopa House'],
                    ],
                ],
            ]);
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()
                ->add('id')
                ->add('name')
                ->add('restaurant_name', fn ($row) => data_get($row, 'category.restaurant.name'));
        }

        public function columns(): array
        {
            return [
                Column::make('Id', 'id'),
                Column::make('Name', 'name'),
                Column::make('Restaurant', 'restaurant_name', 'category.restaurant.name')->searchable(),
            ];
        }
    };

    Livewire::test($component::class)
        ->set('search', 'Not McDonalds')
        ->assertSee('Not McDonalds')
        ->assertDontSee('Sopa House')
        ->set('search', 'Sopa House')
        ->assertSee('Sopa House')
        ->assertDontSee('Not McDonalds');
});
