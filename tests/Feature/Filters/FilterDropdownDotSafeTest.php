<?php

use Illuminate\Support\Facades\Config;
use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\{Column, PowerGridComponent, PowerGridFields};
use PowerComponents\LivewirePowerGrid\Facades\{Filter, PowerGrid};

uses()->group('filters', 'filter-dropdown');

function dotSafeDropdownComponent(string $tableName): PowerGridComponent
{
    return new class($tableName) extends PowerGridComponent
    {
        public function __construct(public string $tableName = 'dropdown-dot') {}

        public function datasource()
        {
            return collect([
                ['id' => 1, 'name' => 'Cheap Dish', 'dishes.name' => 'Cheap Dish'],
                ['id' => 2, 'name' => 'Expensive Dish', 'dishes.name' => 'Expensive Dish'],
            ]);
        }

        public function filters(): array
        {
            return [
                Filter::inputText('name', 'dishes.name'),
            ];
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name');
        }

        public function columns(): array
        {
            return [
                Column::make('Name', 'name', 'dishes.name'),
            ];
        }
    };
}

beforeEach(fn () => Config::set('livewire-powergrid.filter', 'dropdown'));

it('keeps the inline value on the column key after a live update', function () {
    Config::set('livewire-powergrid.filter', 'inline');

    $test = Livewire::test(dotSafeDropdownComponent('inline-dot-persist')::class)
        ->set('filters.name.value', 'Expensive');

    expect($test->get('filters.name.value'))->toBe('Expensive')
        ->and($test->get('filters'))->not->toHaveKey('dishes.name')
        ->and($test->get('filters'))->not->toHaveKey('dishes__pgdot__name');

    $test->assertSee('Expensive Dish')
        ->assertDontSee('Cheap Dish');
});

it('binds deferred filters to the column key, not the qualified dataField', function () {
    $test = Livewire::test(dotSafeDropdownComponent('dropdown-dot-html')::class)
        ->call('loadFilterPanel');

    expect($test->html())->toContain('draftFilters.name.value')
        ->and($test->html())->not->toContain('draftFilters.dishes.name')
        ->and($test->html())->not->toContain('draftFilters.dishes__pgdot__name');
});

it('applies a qualified dataField using the column as the bag key', function () {
    $test = Livewire::test(dotSafeDropdownComponent('dropdown-dot-apply')::class)
        ->set('draftFilters.name.value', 'Expensive')
        ->call('applyFilters');

    expect($test->get('filters'))->toMatchArray(['name' => ['type' => 'input_text', 'value' => 'Expensive']]);

    $test->assertSee('Expensive Dish')
        ->assertDontSee('Cheap Dish');
});

it('re-encodes the applied filters back into the draft on apply', function () {
    $test = Livewire::test(dotSafeDropdownComponent('dropdown-dot-reencode')::class)
        ->set('draftFilters.name.value', 'Expensive')
        ->call('applyFilters');

    expect($test->get('draftFilters'))->toMatchArray(['name' => ['type' => 'input_text', 'value' => 'Expensive']]);
});

it('clears filters data and enabledFilters for a qualified dataField', function () {
    $test = Livewire::test(dotSafeDropdownComponent('dropdown-dot-clear')::class)
        ->set('draftFilters.name.value', 'Expensive')
        ->call('applyFilters');

    expect($test->get('enabledFilters'))->not->toBeEmpty();

    $pillField = data_get($test->get('enabledFilters'), '0.field');

    $test->call('clearFilter', is_string($pillField) ? $pillField : 'name');

    expect($test->get('filters'))->toBeEmpty()
        ->and($test->get('enabledFilters'))->toBeEmpty()
        ->and($test->get('draftFilters'))->toBeEmpty();
});

it('clears filters data when the pill uses the friendly column name', function () {
    $test = Livewire::test(dotSafeDropdownComponent('dropdown-dot-clear-column')::class)
        ->set('draftFilters.name.value', 'Expensive')
        ->call('applyFilters')
        ->call('clearFilter', 'name');

    expect($test->get('filters'))->toBeEmpty()
        ->and($test->get('enabledFilters'))->toBeEmpty();
});

it('re-encodes the applied filters into the draft on reset', function () {
    $test = Livewire::test(dotSafeDropdownComponent('dropdown-dot-reset')::class)
        ->set('draftFilters.name.value', 'Expensive')
        ->call('applyFilters')
        ->set('draftFilters.name.value', 'Cheap')
        ->call('resetFilters');

    expect($test->get('draftFilters'))->toMatchArray(['name' => ['type' => 'input_text', 'value' => 'Expensive']]);
});
