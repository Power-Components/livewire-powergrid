<?php

use PowerComponents\LivewirePowerGrid\{Column, PowerGridComponent};
use PowerComponents\LivewirePowerGrid\DataSource\Processors\Database\Pipelines\{ColumnRawQueries, Sorting};
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Components\{DishesCustomSortTable, DishesNaturalSortTable};
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Models\Dish;

use function PowerComponents\LivewirePowerGrid\Tests\Plugins\livewire;

$payload = 'asc, (SELECT SLEEP(3))';

it('neutralizes SQL injection through sortDirection on a naturalSort column', function () use ($payload) {
    $component = new DishesNaturalSortTable();

    $component->sortField = '';
    $component->sortDirection = $payload;

    $query = Dish::query();

    (new ColumnRawQueries($component))->handle($query, fn ($q) => $q);

    expect($query->toSql())
        ->not->toContain('SLEEP')
        ->toContain(' asc');
});

it('neutralizes SQL injection through sortDirection in a custom sort callback', function () use ($payload) {
    $component = new DishesCustomSortTable();

    $component->sortField = 'price';
    $component->sortDirection = $payload;

    $query = Dish::query();

    (new Sorting($component))->handle($query, fn ($q) => $q);

    expect($query->toSql())->not->toContain('SLEEP');
});

it('normalizes a tampered sortDirection to asc through the Livewire lifecycle', function () use ($payload) {

    livewire(DishesNaturalSortTable::class)
        ->set('sortField', '')
        ->set('sortDirection', $payload)
        ->assertSet('sortDirection', 'asc');
});

it('does not inline component properties other than sortDirection into a raw SQL string', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $search = 'INLINED_VALUE';

        public function columns(): array
        {
            $column = Column::add()->field('name');

            $column->rawQueries = [
                ['method' => 'whereRaw', 'sql' => "name = '{search}'"],
            ];

            return [$column];
        }
    };

    $query = Dish::query();

    (new ColumnRawQueries($component))->handle($query, fn ($q) => $q);

    expect($query->toSql())
        ->toContain('{search}')
        ->not->toContain('INLINED_VALUE');
})->group('database');
