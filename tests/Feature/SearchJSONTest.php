<?php

use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\{Column, Facades\PowerGrid, PowerGridComponent, PowerGridFields};
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Models\Dish;

it('searches JSON column', function () {
    // Setup data
    Dish::query()->forceDelete();
    Dish::create([
        'name' => 'Dish 1',
        'additional' => json_encode(['info' => 'uramaki']),
        'category_id' => 1, 'chef_id' => 1, 'diet' => 1, 'price' => 10, 'calories' => 100, 'stored_at' => '1', 'produced_at' => now(),
    ]);
    Dish::create([
        'name' => 'Dish 2',
        'additional' => json_encode(['info' => 'temaki']),
        'category_id' => 1, 'chef_id' => 1, 'diet' => 1, 'price' => 10, 'calories' => 100, 'stored_at' => '1', 'produced_at' => now(),
    ]);

    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-search-json';

        public function datasource()
        {
            return Dish::query();
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name')->add('additional');
        }

        public function columns(): array
        {
            return [
                Column::make('Name', 'name'),
                Column::make('Additional', 'additional')->searchableJson('dishes'),
            ];
        }
    };

    Livewire::test($component::class)
        ->set('search', 'uramaki')
        ->assertSee('Dish 1')
        ->assertDontSee('Dish 2')
        ->set('search', 'TEMAKI')
        ->assertDontSee('Dish 1')
        ->assertSee('Dish 2');
})->requiresSQLite();
