<?php

use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\{Column, PowerGridComponent, PowerGridFields};
use PowerComponents\LivewirePowerGrid\Facades\{Filter, PowerGrid};

it('properly filters by number', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-number-filter';

        public function datasource()
        {
            return collect([
                ['id' => 1, 'name' => 'Dish 1', 'price' => 100],
                ['id' => 2, 'name' => 'Dish 2', 'price' => 200],
                ['id' => 3, 'name' => 'Dish 3', 'price' => 300],
            ]);
        }

        public function filters(): array
        {
            return [
                Filter::number('price'),
            ];
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name');
        }

        public function columns(): array
        {
            return [Column::make('Name', 'name')];
        }
    };

    Livewire::test($component::class)
        ->set('filters', [
            'number' => ['price' => ['start' => 150, 'end' => 250]],
        ])
        ->assertDontSee('Dish 1')
        ->assertSee('Dish 2')
        ->assertDontSee('Dish 3')
        ->set('filters', [
            'number' => ['price' => ['start' => 250]],
        ])
        ->assertDontSee('Dish 1')
        ->assertDontSee('Dish 2')
        ->assertSee('Dish 3');
});
