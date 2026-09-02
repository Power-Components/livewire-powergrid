<?php

use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\{Column, PowerGridComponent, PowerGridFields};
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Models\Dish;

function seedTabsDishes(): void
{
    Dish::query()->forceDelete();

    $data = [
        ['name' => 'A', 'price' => 100, 'category_id' => 1, 'chef_id' => 1, 'diet' => 1, 'calories' => 100, 'stored_at' => '1', 'produced_at' => now()],
        ['name' => 'B', 'price' => 200, 'category_id' => 1, 'chef_id' => 1, 'diet' => 1, 'calories' => 200, 'stored_at' => '1', 'produced_at' => now()],
        ['name' => 'C', 'price' => 300, 'category_id' => 1, 'chef_id' => 1, 'diet' => 1, 'calories' => 300, 'stored_at' => '1', 'produced_at' => now()],
        ['name' => 'D', 'price' => 400, 'category_id' => 2, 'chef_id' => 1, 'diet' => 1, 'calories' => 400, 'stored_at' => '1', 'produced_at' => now()],
    ];

    foreach ($data as $item) {
        Dish::create($item);
    }
}

function makeTabsComponent(): PowerGridComponent
{
    return new class() extends PowerGridComponent
    {
        public string $tableName = 'test-tabs';

        public function datasource()
        {
            return Dish::query();
        }

        public function setUp(): array
        {
            return [
                PowerGrid::tabs()
                    ->add('all', label: 'All')
                    ->add('cat1', label: 'Category 1', scope: ['category_id', 1])
                    ->add('cat2', label: 'Category 2', scope: ['category_id', '=', 2], badge: false)
                    ->default('cat1'),
            ];
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name')->add('category_id');
        }

        public function columns(): array
        {
            return [
                Column::make('Id', 'id'),
                Column::make('Name', 'name'),
                Column::make('Category', 'category_id'),
            ];
        }
    };
}

it('applies the default tab on mount', function () {
    seedTabsDishes();

    Livewire::test(makeTabsComponent()::class)
        ->assertSet('activeTab', 'cat1');
})->requiresSQLite();

it('scopes the rows to the active tab', function () {
    seedTabsDishes();

    Livewire::test(makeTabsComponent()::class)
        // default cat1 => 3 rows
        ->assertSee('Category 1')
        ->call('selectTab', 'cat2')
        ->assertSet('activeTab', 'cat2')
        ->tap(function ($test) {
            $rows = $test->instance()->records;
            expect($rows->total())->toBe(1);
        })
        ->call('selectTab', 'all')
        ->tap(function ($test) {
            expect($test->instance()->records->total())->toBe(4);
        });
})->requiresSQLite();

it('computes badge counts in a single batched query', function () {
    seedTabsDishes();

    $tabQueries = 0;

    DB::listen(function ($query) use (&$tabQueries) {
        if (str_contains($query->sql, 'pg_tab_')) {
            $tabQueries++;
        }
    });

    Livewire::test(makeTabsComponent()::class)
        ->tap(function ($test) {
            $data = collect($test->instance()->tabsData()['tabs'])->keyBy('key');

            // all => count(*) = 4, cat1 => 3
            expect($data['all']['badge'])->toBe(4)
                ->and($data['cat1']['badge'])->toBe(3)
                // cat2 declared badge:false => hidden
                ->and($data['cat2']['badge'])->toBeNull();
        });

    expect($tabQueries)->toBe(1);
})->requiresSQLite();

it('keeps activeTab server-owned (locked against client updates)', function () {
    seedTabsDishes();

    Livewire::test(makeTabsComponent()::class)
        ->set('activeTab', 'cat2');
})->throws(Exception::class, 'Cannot update locked property: [activeTab]')->requiresSQLite();

it('ignores a tab scope on an undeclared column', function () {
    seedTabsDishes();

    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-tabs-guard';

        public function datasource()
        {
            return Dish::query();
        }

        public function setUp(): array
        {
            return [
                PowerGrid::tabs()
                    ->add('all', label: 'All')
                    // "secret" is NOT a declared column => scope must be ignored
                    ->add('evil', label: 'Evil', scope: ['secret', 'x'])
                    ->default('evil'),
            ];
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name');
        }

        public function columns(): array
        {
            return [Column::make('Id', 'id'), Column::make('Name', 'name')];
        }
    };

    Livewire::test($component::class)
        ->tap(function ($test) {
            expect($test->instance()->records->total())->toBe(4);
        });
})->requiresSQLite();
