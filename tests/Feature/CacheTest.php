<?php

use Illuminate\Auth\GenericUser;
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

function scopedCacheTestComponent(): string
{
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-cache-scope';

        public static string $scope = '';

        public function setUp(): array
        {
            return [PowerGrid::cache()];
        }

        public function datasource()
        {
            return Dish::query();
        }

        public function transformQuery(mixed $query): mixed
        {
            return $query->where('name', 'like', self::$scope.'%');
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name')->add('price');
        }

        public function columns(): array
        {
            return [
                Column::make('Name', 'name'),
                Column::make('Price', 'price')->summarize('sum', 'Sum', true, true),
            ];
        }
    };

    return $component::class;
}

describe('cache scope', function () {
    it('does not serve one user cached records to another user', function () {
        $component = cacheTestComponent(cacheEnabled: true);

        createCacheTestDish('First Dish');
        $this->actingAs(new GenericUser(['id' => 1]));
        Livewire::test($component)->assertSee('First Dish');

        createCacheTestDish('Second Dish');
        $this->actingAs(new GenericUser(['id' => 2]));
        Livewire::test($component)->assertSee('Second Dish');

        $this->actingAs(new GenericUser(['id' => 1]));
        Livewire::test($component)->assertDontSee('Second Dish');
    });

    it('does not serve cached records or totals across transformQuery scopes', function () {
        $component = scopedCacheTestComponent();

        createCacheTestDish('Pizza');
        createCacheTestDish('Sushi');
        Dish::query()->where('name', 'Sushi')->update(['price' => 30]);

        $component::$scope = 'Pizza';
        Livewire::test($component)
            ->assertSee('Pizza')
            ->assertDontSee('Sushi')
            ->assertSee('Sum: 10');

        $component::$scope = 'Sushi';
        Livewire::test($component)
            ->assertSee('Sushi')
            ->assertDontSee('Pizza')
            ->assertSee('Sum: 30');
    });
});
