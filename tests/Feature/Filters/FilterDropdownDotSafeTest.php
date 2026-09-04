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

it('renders a dot-safe deferred wire:model for a qualified column', function () {
    $test = Livewire::test(dotSafeDropdownComponent('dropdown-dot-html')::class)
        ->call('loadFilterPanel');

    expect($test->html())->toContain('draftFilters.input_text.dishes__pgdot__name')
        ->and($test->html())->not->toContain('draftFilters.input_text.dishes.name');
});

it('decodes the dot-safe draft key back to the real column on apply', function () {
    $test = Livewire::test(dotSafeDropdownComponent('dropdown-dot-apply')::class)
        ->set('draftFilters.input_text.dishes__pgdot__name', 'Expensive')
        ->call('applyFilters');

    expect($test->get('filters'))->toBe(['input_text' => ['dishes.name' => 'Expensive']]);
});

it('re-encodes the applied filters back into the draft on apply', function () {
    $test = Livewire::test(dotSafeDropdownComponent('dropdown-dot-reencode')::class)
        ->set('draftFilters.input_text.dishes__pgdot__name', 'Expensive')
        ->call('applyFilters');

    expect($test->get('draftFilters'))->toBe(['input_text' => ['dishes__pgdot__name' => 'Expensive']]);
});

it('clears filters data and enabledFilters for a qualified dataField', function () {
    $test = Livewire::test(dotSafeDropdownComponent('dropdown-dot-clear')::class)
        ->set('draftFilters.input_text.dishes__pgdot__name', 'Expensive')
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
        ->set('draftFilters.input_text.dishes__pgdot__name', 'Expensive')
        ->call('applyFilters')
        ->call('clearFilter', 'name');

    expect($test->get('filters'))->toBeEmpty()
        ->and($test->get('enabledFilters'))->toBeEmpty();
});

it('re-encodes the applied filters into the draft on reset', function () {
    $test = Livewire::test(dotSafeDropdownComponent('dropdown-dot-reset')::class)
        ->set('draftFilters.input_text.dishes__pgdot__name', 'Expensive')
        ->call('applyFilters')
        ->set('draftFilters.input_text.dishes__pgdot__name', 'Cheap')
        ->call('resetFilters');

    expect($test->get('draftFilters'))->toBe(['input_text' => ['dishes__pgdot__name' => 'Expensive']]);
});
