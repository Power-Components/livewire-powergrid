<?php

use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\{Column, Facades\PowerGrid, PowerGridComponent, PowerGridFields};

it('properly displays row index', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-row-index';

        public function datasource()
        {
            return collect([
                ['id' => 1, 'name' => 'Dish A'],
                ['id' => 2, 'name' => 'Dish B'],
                ['id' => 3, 'name' => 'Dish C'],
            ]);
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
                Column::make('Index', 'index')->index(),
                Column::make('Id', 'id'),
                Column::make('Name', 'name')->searchable(),
            ];
        }
    };

    Livewire::test($component::class)
        ->assertSeeInOrder([
            '1', 'Dish A',
            '2', 'Dish B',
            '3', 'Dish C',
        ])
        ->set('search', 'Dish B')
        ->assertSeeInOrder([
            '1', 'Dish B',
        ])
        ->assertDontSee('Dish A')
        ->assertDontSee('Dish C')
        ->set('search', '')
        ->assertSeeInOrder([
            '1', 'Dish A',
            '2', 'Dish B',
            '3', 'Dish C',
        ]);
});
