<?php

use Illuminate\Support\Facades\{DB, Schema};
use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\{Column, PowerGridComponent, PowerGridFields};
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Models\{Chef, Dish};

function seedSummaryDishes(): void
{
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
}

it('collapses every aggregate into a single batched query', function () {
    seedSummaryDishes();

    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-summaries-batched';

        public function datasource()
        {
            return Dish::query();
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name')->add('price');
        }

        public function columns(): array
        {
            return [
                Column::make('Id', 'id')->withCount('Count', true, true),
                Column::make('Price', 'price')
                    ->withSum('Sum', true, true)
                    ->withAvg('Avg', true, true)
                    ->withMin('Min', true, true)
                    ->withMax('Max', true, true),
            ];
        }
    };

    $aggregateQueries = 0;

    DB::listen(function ($query) use (&$aggregateQueries) {
        if (str_contains($query->sql, 'turbine_summary_')) {
            $aggregateQueries++;
        }
    });

    Livewire::test($component::class)
        ->assertSee('Sum: 1000')
        ->assertSee('Avg: 250')
        ->assertSee('Min: 100')
        ->assertSee('Max: 400')
        ->assertSee('Count: 4');

    // 5 aggregates across 2 columns previously meant 5 separate queries.
    expect($aggregateQueries)->toBe(1);
})->requiresSQLite();

it('keeps showing totals when the dataset is served from cache', function () {
    seedSummaryDishes();

    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-summaries-cache';

        public function setUp(): array
        {
            return [
                PowerGrid::cache(),
            ];
        }

        public function datasource()
        {
            return Dish::query();
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('price');
        }

        public function columns(): array
        {
            return [
                Column::make('Price', 'price')->withSum('Sum', true, true),
            ];
        }
    };

    // First mount: warms both the records cache and the summaries cache.
    Livewire::test($component::class)
        ->assertSee('Sum: 1000');

    // Fresh mount: the records pipeline must be skipped (records cache hit), so
    // no aggregate query runs and totals come from the dedicated summaries cache.
    $aggregateQueries = 0;
    DB::listen(function ($query) use (&$aggregateQueries) {
        if (str_contains($query->sql, 'turbine_summary_')) {
            $aggregateQueries++;
        }
    });

    Livewire::test($component::class)
        ->assertSee('Sum: 1000');

    expect($aggregateQueries)->toBe(0);
})->requiresSQLite();

it('defaults summaries to the footer when flags are omitted', function () {
    seedSummaryDishes();

    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-summaries-defaults';

        public function datasource()
        {
            return Dish::query();
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('price');
        }

        public function columns(): array
        {
            return [
                Column::make('Price', 'price')->withSum('Sum'),
            ];
        }
    };

    Livewire::test($component::class)
        ->assertSet('footerTotalColumn', true)
        ->assertSet('headerTotalColumn', false)
        ->assertSee('Sum: 1000');
})->requiresSQLite();

it('keeps the data rows when summarizing an Eloquent model without global scopes', function () {
    // Regression: models without scopes (e.g. no SoftDeletes) made EloquentBuilder::toBase()
    // return the original query, so the batched aggregate select replaced the data query and
    // only the summary row rendered.
    Schema::disableForeignKeyConstraints();
    DB::table('chefs')->truncate();
    Schema::enableForeignKeyConstraints();

    DB::table('chefs')->insert([
        ['name' => 'Luan', 'restaurant_id' => 1],
        ['name' => 'Dan', 'restaurant_id' => 1],
        ['name' => 'Vitor', 'restaurant_id' => 1],
        ['name' => 'Claudio', 'restaurant_id' => 1],
    ]);

    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-summaries-no-scope';

        public function datasource()
        {
            return Chef::query();
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name');
        }

        public function columns(): array
        {
            return [
                Column::make('Id', 'id')->withSum('Id'),
                Column::make('Name', 'name'),
            ];
        }
    };

    Livewire::test($component::class)
        ->assertSee('Id: 10')   // summary still computed
        ->assertSee('Luan')     // data rows still rendered
        ->assertSee('Claudio');
})->requiresSQLite();

it('computes a custom summary from a closure honoring the active filter', function () {
    seedSummaryDishes();

    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'test-summaries-custom';

        public function datasource()
        {
            return Dish::query();
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name')->add('price');
        }

        public function columns(): array
        {
            return [
                Column::make('Name', 'name')->searchable(),
                Column::make('Price', 'price')
                    ->withSummary('expensive', 'Expensive', fn ($query) => $query->where('price', '>=', 300)->count(), true, true),
            ];
        }
    };

    Livewire::test($component::class)
        ->assertSee('Expensive: 2')
        ->set('search', 'Vitor')
        ->assertSee('Expensive: 1');
})->requiresSQLite();
