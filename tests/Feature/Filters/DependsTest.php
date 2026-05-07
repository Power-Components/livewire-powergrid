<?php

use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\{Column, Facades\PowerGrid, PowerGridComponent, PowerGridFields};
use PowerComponents\LivewirePowerGrid\Facades\Filter;

it('"depends" works properly in select', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-depends';

        public function datasource()
        {
            return collect([
                ['id' => 1, 'name' => 'Dish 1', 'category_id' => 1, 'chef_id' => 1],
                ['id' => 2, 'name' => 'Dish 2', 'category_id' => 2, 'chef_id' => 2],
            ]);
        }

        public function setUp(): array
        {
            return [
                PowerGrid::header()->showSearchInput(),
            ];
        }

        public function filters(): array
        {
            return [
                Filter::select('category_id')
                    ->dataSource(collect([['id' => 1, 'name' => 'Cat 1'], ['id' => 2, 'name' => 'Cat 2']]))
                    ->optionValue('id')
                    ->optionLabel('name'),

                Filter::select('chef_id')
                    ->depends(['category_id'])
                    ->dataSource(function ($depends) {
                        $catId = data_get($depends, 'category_id');
                        if ($catId == 1) {
                            return collect([['id' => 1, 'name' => 'Chef for Cat 1']]);
                        }
                        if ($catId == 2) {
                            return collect([['id' => 2, 'name' => 'Chef for Cat 2']]);
                        }

                        return collect([]);
                    })
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
            return [
                Column::make('Name', 'name'),
                Column::make('Category', 'category_id'),
                Column::make('Chef', 'chef_id'),
            ];
        }
    };

    Livewire::test($component::class)
        ->set('filters.select.category_id', 1)
        ->assertSeeHtml('Chef for Cat 1')
        ->assertDontSeeHtml('Chef for Cat 2')
        ->set('filters.select.category_id', 2)
        ->assertDontSeeHtml('Chef for Cat 1')
        ->assertSeeHtml('Chef for Cat 2');
});
