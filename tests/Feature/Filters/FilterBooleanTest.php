<?php

use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\{Column, Facades\PowerGrid, PowerGridComponent, PowerGridFields};
use PowerComponents\LivewirePowerGrid\Facades\Filter;

it('properly filters by boolean', function (string $value, array $see, array $dontSee) {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-boolean-filter';

        public function datasource()
        {
            return collect([
                ['id' => 1, 'name' => 'Dish 1', 'in_stock' => true],
                ['id' => 2, 'name' => 'Dish 2', 'in_stock' => false],
            ]);
        }

        public function setUp(): array
        {
            return [PowerGrid::header()->showSearchInput()];
        }

        public function filters(): array
        {
            return [
                Filter::boolean('in_stock')->label('yes', 'no'),
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

    $lw = Livewire::test($component::class)
        ->set('filters', ['boolean' => ['in_stock' => $value]]);

    foreach ($see as $item) {
        $lw->assertSee($item);
    }
    foreach ($dontSee as $item) {
        $lw->assertDontSee($item);
    }

    if ($value !== 'all') {
        $lw->call('clearFilter', 'in_stock')
            ->assertSee('Dish 1')
            ->assertSee('Dish 2');
    }
})->with([
    'true' => ['value' => 'true', 'see' => ['Dish 1'], 'dontSee' => ['Dish 2']],
    'false' => ['value' => 'false', 'see' => ['Dish 2'], 'dontSee' => ['Dish 1']],
    'all' => ['value' => 'all', 'see' => ['Dish 1', 'Dish 2'], 'dontSee' => []],
]);

it('properly filters by boolean using custom collection logic', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-boolean-custom';

        public function datasource()
        {
            return collect([
                ['id' => 1, 'name' => 'Dish 1', 'in_stock' => true],
                ['id' => 2, 'name' => 'Dish 2', 'in_stock' => false],
            ]);
        }

        public function filters(): array
        {
            return [
                Filter::boolean('in_stock')
                    ->collection(function ($collection, $value) {
                        return $collection->where('id', 1);
                    }),
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
        ->set('filters', ['boolean' => ['in_stock' => 'true']])
        ->assertSee('Dish 1')
        ->assertDontSee('Dish 2');
});
