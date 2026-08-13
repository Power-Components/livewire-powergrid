<?php

use Illuminate\Pagination\LengthAwarePaginator;
use PowerComponents\LivewirePowerGrid\{Column, PowerGridFields};
use PowerComponents\LivewirePowerGrid\Components\Filters\FilterBase;
use PowerComponents\LivewirePowerGrid\DataSource\ProcessDataSource;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Support\State\{ArrayGridContext, PowerGridState};
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Models\Dish;

/**
 * These tests exercise the data engine (search / filter / sort / paginate)
 * through a plain PHP {@see ArrayGridContext} with NO Livewire component booted
 * and no Livewire::test() — proving the backend is usable headless (Inertia/React).
 */
function headlessFields(): PowerGridFields
{
    return (new PowerGridFields())
        ->add('id')
        ->add('name')
        ->add('price');
}

/** @return array<int, Column> */
function headlessColumns(): array
{
    return [
        Column::add()->title('Id')->field('id')->sortable(),
        Column::add()->title('Name')->field('name')->searchable()->sortable(),
        Column::add()->title('Price')->field('price')->sortable(),
    ];
}

/**
 * @param  array<string, mixed>  $statePayload
 * @param  array<int, FilterBase>  $filters
 */
function headlessContext(array $statePayload = [], ?callable $datasource = null, array $filters = []): ArrayGridContext
{
    $state = PowerGridState::fromArray(array_merge([
        'primaryKey' => 'id',
        'tableName' => 'dishes',
        'sortField' => 'id',
        'setUp' => ['footer' => ['perPage' => 100, 'pageName' => 'page']],
    ], $statePayload));

    return new ArrayGridContext(
        state: $state,
        datasourceResolver: $datasource ?? fn () => Dish::query(),
        fields: headlessFields(),
        columns: headlessColumns(),
        filters: $filters,
    );
}

it('paginates an eloquent datasource with no livewire component', function () {
    $context = headlessContext();

    $result = ProcessDataSource::make($context)->get();
    $paginator = $result['results'];

    expect($paginator)->toBeInstanceOf(LengthAwarePaginator::class)
        ->and($paginator->total())->toBe(Dish::query()->count())
        ->and($context->getCurrentTable())->toBe('dishes');
});

it('applies global search headlessly', function () {
    $context = headlessContext(['search' => 'Pastel']);

    $paginator = ProcessDataSource::make($context)->get()['results'];

    expect($paginator->total())->toBe(Dish::query()->where('name', 'like', '%Pastel%')->count())
        ->and($paginator->total())->toBeGreaterThan(0)
        ->and($paginator->getCollection()->pluck('name')->first())->toContain('Pastel');
});

it('orders results headlessly', function () {
    $context = headlessContext(['sortField' => 'price', 'sortDirection' => 'desc']);

    $paginator = ProcessDataSource::make($context)->get()['results'];

    $expectedTop = Dish::query()->max('price');

    expect((float) $paginator->getCollection()->first()->price)->toBe((float) $expectedTop);
});

it('applies a column filter headlessly', function () {
    $context = headlessContext(
        statePayload: ['filters' => ['input_text' => ['name' => 'Pastel']]],
        filters: [Filter::inputText('name')],
    );

    $paginator = ProcessDataSource::make($context)->get()['results'];

    expect($paginator->total())->toBe(Dish::query()->where('name', 'like', '%Pastel%')->count());
});

it('runs search and sort over a collection datasource headlessly', function () {
    $rows = collect([
        ['id' => 1, 'name' => 'Sushi', 'price' => 30.0],
        ['id' => 2, 'name' => 'Pastel de Nata', 'price' => 10.0],
        ['id' => 3, 'name' => 'Pastel de Belém', 'price' => 20.0],
    ]);

    $context = headlessContext(
        statePayload: ['search' => 'Pastel', 'sortField' => 'price', 'sortDirection' => 'asc'],
        datasource: fn () => $rows,
    );

    $result = ProcessDataSource::make($context)->get();
    $collection = $result['results']->getCollection();

    expect($result['results']->total())->toBe(2)
        ->and($collection->pluck('name')->all())->toBe(['Pastel de Nata', 'Pastel de Belém']);
});

it('ignores an undeclared filter field (mass-assignment guard) headlessly', function () {
    // 'price' is a real column but NOT declared as a filter, so a crafted
    // filter payload for it must be ignored — total stays the full set.
    $context = headlessContext(
        statePayload: ['filters' => ['input_text' => ['price' => '10']]],
        filters: [Filter::inputText('name')],
    );

    $paginator = ProcessDataSource::make($context)->get()['results'];

    expect($paginator->total())->toBe(Dish::query()->count());
});
