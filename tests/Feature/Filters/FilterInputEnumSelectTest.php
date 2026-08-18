<?php

use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\{Column, PowerGridComponent, PowerGridFields};
use PowerComponents\LivewirePowerGrid\Facades\{Filter, PowerGrid};

it('properly filters by enum-like select', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-enum-filter';

        public function datasource()
        {
            return collect([
                ['id' => 1, 'name' => 'Dish 1', 'diet' => 1],
                ['id' => 2, 'name' => 'Dish 2', 'diet' => 2],
                ['id' => 3, 'name' => 'Dish 3', 'diet' => 1],
            ]);
        }

        public function filters(): array
        {
            return [
                Filter::select('diet')
                    ->dataSource(collect([
                        ['diet' => 1, 'name' => 'Vegan'],
                        ['diet' => 2, 'name' => 'Celiac'],
                    ]))
                    ->optionValue('diet')
                    ->optionLabel('name'),
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
            'select' => ['diet' => 1],
        ])
        ->assertSee('Dish 1')
        ->assertDontSee('Dish 2')
        ->assertSee('Dish 3')
        ->set('filters', [
            'select' => ['diet' => 2],
        ])
        ->assertDontSee('Dish 1')
        ->assertSee('Dish 2')
        ->assertDontSee('Dish 3');
});
