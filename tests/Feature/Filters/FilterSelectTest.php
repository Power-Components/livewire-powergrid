<?php

use Livewire\Attributes\Computed;
use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\{Column, PowerGridComponent, PowerGridFields};
use PowerComponents\LivewirePowerGrid\Facades\{Filter, PowerGrid};

it('properly filters by select', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-select-filter';

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
                Filter::select('category_id')
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
            'select' => ['category_id' => 1],
        ])
        ->assertSee('Dish 1')
        ->assertDontSee('Dish 2')
        ->assertDontSee('Dish 3')
        ->set('filters', [
            'select' => ['category_id' => 2],
        ])
        ->assertDontSee('Dish 1')
        ->assertSee('Dish 2')
        ->assertDontSee('Dish 3');
});

it('properly filters by select using computed datasource', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-select-computed';

        public function datasource()
        {
            return collect([
                ['id' => 1, 'name' => 'Dish 1', 'category_id' => 1],
            ]);
        }

        #[Computed]
        public function getCats()
        {
            return collect([['id' => 1, 'name' => 'Cat 1']]);
        }

        public function filters(): array
        {
            return [
                Filter::select('category_id')
                    ->computedDatasource('getCats')
                    ->optionValue('id')
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
        ->assertSee('Dish 1')
        ->set('filters', ['select' => ['category_id' => 1]])
        ->assertSee('Dish 1');
});
