<?php

use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\{Column, PowerGridComponent, PowerGridFields};
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Models\Order;
use PowerComponents\LivewirePowerGrid\Tests\Concerns\TestDatabase;

uses()->group('sort', 'multisort');

/**
 * Multi-column sorting (multiSort + sortArray) had no ordering tests on either
 * the Collection or the Database sorting pipeline. These lock the tie-break
 * behaviour: the first field in sortArray is primary, the next breaks ties.
 */
it('sorts a collection by multiple columns respecting declaration order', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-multisort-collection';

        public bool $multiSort = true;

        public function datasource()
        {
            return collect([
                ['id' => 1, 'name' => 'Row Beta 10', 'grp' => 'B', 'price' => 10],
                ['id' => 2, 'name' => 'Row Alpha 30', 'grp' => 'A', 'price' => 30],
                ['id' => 3, 'name' => 'Row Alpha 20', 'grp' => 'A', 'price' => 20],
                ['id' => 4, 'name' => 'Row Beta 5', 'grp' => 'B', 'price' => 5],
            ]);
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name')->add('grp')->add('price');
        }

        public function columns(): array
        {
            return [
                Column::make('Name', 'name'),
                Column::make('Group', 'grp')->sortable(),
                Column::make('Price', 'price')->sortable(),
            ];
        }
    };

    // grp ASC (primary), then price DESC (tie-break)
    Livewire::test($component::class)
        ->set('sortArray', ['grp' => 'asc', 'price' => 'desc'])
        ->assertSeeInOrder([
            'Row Alpha 30',
            'Row Alpha 20',
            'Row Beta 10',
            'Row Beta 5',
        ]);
});

it('sorts a database query by multiple columns respecting declaration order', function () {
    TestDatabase::up();

    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-multisort-database';

        public bool $multiSort = true;

        public function datasource()
        {
            return Order::query();
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name')->add('price')->add('is_active');
        }

        public function columns(): array
        {
            return [
                Column::make('Name', 'name'),
                Column::make('Active', 'is_active')->sortable(),
                Column::make('Price', 'price')->sortable(),
            ];
        }
    };

    // is_active ASC (inactive first), then price DESC (tie-break)
    Livewire::test($component::class)
        ->set('sortArray', ['is_active' => 'asc', 'price' => 'desc'])
        ->assertSeeInOrder([
            'Order 3', // is_active = false
            'Order 2', // is_active = true, price 20
            'Order 1', // is_active = true, price 10
        ]);
});
