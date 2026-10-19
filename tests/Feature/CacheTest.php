<?php

use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\{Column, PowerGridComponent, PowerGridFields};
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Models\Dish;

function cacheTestComponent(bool $cacheEnabled): string
{
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-cache-setup';

        public static bool $cacheEnabled = true;

        public function setUp(): array
        {
            $cache = PowerGrid::cache();

            return [self::$cacheEnabled ? $cache : $cache->disabled()];
        }

        public function datasource()
        {
            return Dish::query();
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

    $component::$cacheEnabled = $cacheEnabled;

    return $component::class;
}

function createCacheTestDish(string $name): void
{
    Dish::create(['name' => $name, 'price' => 10, 'category_id' => 1, 'chef_id' => 1, 'diet' => 1, 'calories' => 100, 'stored_at' => '1', 'produced_at' => now()]);
}

beforeEach(fn () => Dish::query()->forceDelete());

it('serves records from cache when cache is enabled', function () {
    $component = cacheTestComponent(cacheEnabled: true);

    createCacheTestDish('First Dish');
    Livewire::test($component)->assertSee('First Dish');

    createCacheTestDish('Second Dish');
    Livewire::test($component)->assertDontSee('Second Dish');
});

it('queries fresh records when cache is disabled', function () {
    $component = cacheTestComponent(cacheEnabled: false);

    createCacheTestDish('First Dish');
    Livewire::test($component)->assertSee('First Dish');

    createCacheTestDish('Second Dish');
    Livewire::test($component)->assertSee('Second Dish');
});
