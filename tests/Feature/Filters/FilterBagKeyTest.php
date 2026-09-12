<?php

use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\{Column, PowerGridComponent, PowerGridFields};
use PowerComponents\LivewirePowerGrid\Facades\{Filter, PowerGrid};

uses()->group('filters');

function bagKeyComponent(string $tableName): PowerGridComponent
{
    return new class($tableName) extends PowerGridComponent
    {
        public function __construct(public string $tableName = 'bag-key') {}

        public function datasource()
        {
            return collect([
                ['id' => 1, 'name' => 'Cheap Dish', 'dishes.name' => 'Cheap Dish', 'price' => 10],
                ['id' => 2, 'name' => 'Expensive Dish', 'dishes.name' => 'Expensive Dish', 'price' => 500],
            ]);
        }

        public function filters(): array
        {
            return [
                Filter::inputText('name', 'dishes.name'),
                Filter::number('price'),
            ];
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name')->add('price');
        }

        public function columns(): array
        {
            return [Column::make('Name', 'name', 'dishes.name'), Column::make('Price', 'price')];
        }
    };
}

it('folds the SQL field key onto the filter bag key', function () {
    $test = Livewire::test(bagKeyComponent('bag-key-field')::class)
        ->set('filters', ['dishes.name' => ['type' => 'input_text', 'value' => 'Expensive']]);

    expect($test->get('filters'))->toHaveKey('name')
        ->and($test->get('filters'))->not->toHaveKey('dishes.name');
});

it('folds an encoded field key onto the filter bag key', function () {
    $test = Livewire::test(bagKeyComponent('bag-key-encoded')::class)
        ->set('filters', ['dishes__pgdot__name' => ['type' => 'input_text', 'value' => 'Expensive']]);

    expect($test->get('filters'))->toHaveKey('name')
        ->and($test->get('filters'))->not->toHaveKey('dishes__pgdot__name');

    $test->assertSee('Expensive Dish')->assertDontSee('Cheap Dish');
});

it('clears a number filter through either bound alias', function () {
    $test = Livewire::test(bagKeyComponent('bag-key-bounds')::class)
        ->set('filters.price.value.start', '100')
        ->call('clearFilter', 'price_start');

    expect($test->get('filters'))->not->toHaveKey('price')
        ->and($test->get('enabledFilters'))->toBeEmpty();
});

it('ignores a bag key no filter declares', function () {
    $test = Livewire::test(bagKeyComponent('bag-key-guard')::class)
        ->set('filters', ['secret' => ['type' => 'input_text', 'value' => 'x']]);

    $test->assertSee('Cheap Dish')->assertSee('Expensive Dish');
});

it('keeps a chosen operator after the value is cleared and reuses it on the next value', function () {
    $test = Livewire::test(bagKeyComponent('bag-key-operator')::class)
        ->set('filters.name.op', 'is')
        ->set('filters.name.value', 'Expensive Dish')
        ->set('filters.name.value', '');

    expect($test->get('filters'))->not->toHaveKey('name')
        ->and(data_get($test->get('filterOperators'), 'name'))->toBe('is');

    $test->set('filters.name.value', 'Cheap Dish');

    expect(data_get($test->get('filters'), 'name.op'))->toBe('is');
});
