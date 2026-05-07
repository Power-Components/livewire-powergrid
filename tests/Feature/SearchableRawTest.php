<?php

use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\{Column, Facades\PowerGrid, PowerGridComponent, PowerGridFields};
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Models\Dish;

it('searches data using whereRaw on sqlite', function () {
    // Setup data
    Dish::query()->forceDelete();
    Dish::create(['name' => 'Dish 1', 'produced_at' => '2021-09-09', 'category_id' => 1, 'chef_id' => 1, 'diet' => 1, 'price' => 10, 'calories' => 100, 'stored_at' => '1']);
    Dish::create(['name' => 'Dish 2', 'produced_at' => '2021-10-10', 'category_id' => 1, 'chef_id' => 1, 'diet' => 1, 'price' => 10, 'calories' => 100, 'stored_at' => '1']);

    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-searchable-raw';

        public function datasource()
        {
            return Dish::query();
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()
                ->add('id')
                ->add('name')
                ->add('produced_at_formatted', fn ($row) => $row->produced_at->format('d/m/Y'));
        }

        public function columns(): array
        {
            return [
                Column::make('Name', 'name'),
                Column::make('Produced At', 'produced_at_formatted', 'produced_at')
                    ->searchableRaw('STRFTIME("%d/%m/%Y", dishes.produced_at) like ?'),
            ];
        }
    };

    Livewire::test($component::class)
        ->set('search', '09/09/2021')
        ->assertSee('Dish 1')
        ->assertDontSee('Dish 2')
        ->set('search', '10/10/2021')
        ->assertDontSee('Dish 1')
        ->assertSee('Dish 2');
})->requiresSQLite();
