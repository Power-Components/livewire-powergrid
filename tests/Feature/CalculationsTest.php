<?php

use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\{Column, PowerGridComponent, PowerGridFields};
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Models\Dish;

it('calculates properly from database', function () {
    // Setup data
    Dish::query()->forceDelete();
    $data = [
        ['name' => 'Luan', 'price' => 100, 'category_id' => 1, 'chef_id' => 1, 'diet' => 1, 'calories' => 100, 'stored_at' => '1', 'produced_at' => now()],
        ['name' => 'Daniel', 'price' => 200, 'category_id' => 1, 'chef_id' => 1, 'diet' => 1, 'calories' => 200, 'stored_at' => '1', 'produced_at' => now()],
        ['name' => 'Claudio', 'price' => 300, 'category_id' => 1, 'chef_id' => 1, 'diet' => 1, 'calories' => 300, 'stored_at' => '1', 'produced_at' => now()],
        ['name' => 'Vitor', 'price' => 400, 'category_id' => 1, 'chef_id' => 1, 'diet' => 1, 'calories' => 400, 'stored_at' => '1', 'produced_at' => now()],
    ];
    foreach ($data as $item) {
        Dish::create($item);
    }

    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-calculations-db';

        public function datasource()
        {
            return Dish::query();
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()
                ->add('id')
                ->add('name')
                ->add('price');
        }

        public function columns(): array
        {
            return [
                Column::make('Id', 'id')->withCount('Count Id', true, true),
                Column::make('Name', 'name')->searchable(),
                Column::make('Price', 'price')
                    ->withSum('Sum Price', true, true)
                    ->withAvg('Avg Price', true, true)
                    ->withMin('Min Price', true, true)
                    ->withMax('Max Price', true, true),
            ];
        }
    };

    Livewire::test($component::class)
        ->assertSee('Count Id: 4')
        ->assertSee('Sum Price: 1000')
        ->assertSee('Avg Price: 250')
        ->assertSee('Min Price: 100')
        ->assertSee('Max Price: 400')

        ->set('search', 'Luan')
        ->assertSee('Count Id: 1')
        ->assertSee('Sum Price: 100')
        ->assertSee('Avg Price: 100')
        ->assertSee('Min Price: 100')
        ->assertSee('Max Price: 100');
})->requiresSQLite();
