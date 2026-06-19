<?php

use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\{Column, Facades\PowerGrid, PowerGridComponent, PowerGridFields};
use PowerComponents\LivewirePowerGrid\Facades\Filter;

it('properly filters by multi_select', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-multi-select';

        public function datasource()
        {
            return collect([
                ['id' => 1, 'name' => 'Dish 1', 'category_id' => 1],
                ['id' => 2, 'name' => 'Dish 2', 'category_id' => 2],
                ['id' => 3, 'name' => 'Dish 3', 'category_id' => 3],
            ]);
        }

        public function filters(): array
        {
            return [
                Filter::multiSelect('category_id')
                    ->dataSource(collect([
                        ['category_id' => 1, 'name' => 'Cat 1'],
                        ['category_id' => 2, 'name' => 'Cat 2'],
                        ['category_id' => 3, 'name' => 'Cat 3'],
                    ]))
                    ->optionValue('category_id')
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
            'multi_select' => ['category_id' => [1, 2]],
        ])
        ->assertSee('Dish 1')
        ->assertSee('Dish 2')
        ->assertDontSee('Dish 3')
        ->set('filters', [
            'multi_select' => ['category_id' => [3]],
        ])
        ->assertDontSee('Dish 1')
        ->assertDontSee('Dish 2')
        ->assertSee('Dish 3');
});

it('ignores empty values instead of dropping the multi_select filter', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-multi-select-empty';

        public function datasource()
        {
            return collect([
                ['id' => 1, 'name' => 'Dish 1', 'category_id' => 1],
                ['id' => 2, 'name' => 'Dish 2', 'category_id' => 2],
            ]);
        }

        public function filters(): array
        {
            return [Filter::multiSelect('category_id')];
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name')->add('category_id');
        }

        public function columns(): array
        {
            return [Column::make('Name', 'name'), Column::make('Category', 'category_id')];
        }
    };

    Livewire::test($component::class)
        ->set('filters', [
            'multi_select' => ['category_id' => ['', 1]],
        ])
        ->assertSee('Dish 1')
        ->assertDontSee('Dish 2');
});
