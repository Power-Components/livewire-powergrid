<?php

use Illuminate\Support\Facades\Config;
use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\{Column, PowerGridComponent, PowerGridFields};
use PowerComponents\LivewirePowerGrid\Facades\{Filter, PowerGrid};
use PowerComponents\LivewirePowerGrid\FilterAttributes\FilterWireAttributes;
use PowerComponents\LivewirePowerGrid\Support\FilterKey;

uses()->group('filters');

it('resolves the wire bag key from a filter definition', function () {
    expect(FilterKey::fromFilter(['column' => 'name', 'field' => 'name']))->toBe('name')
        ->and(FilterKey::fromFilter(['column' => 'name', 'field' => 'dishes.name']))->toBe('name')
        ->and(FilterKey::fromFilter(['column' => 'dishes.name', 'field' => 'dishes.name']))->toBe('dishes__pgdot__name')
        ->and(FilterKey::fromFilter(Filter::inputText('name', 'dishes.name')))->toBe('name');
});

it('prefers a stamped modelKey over column and field', function () {
    expect(FilterKey::fromFilter([
        'column' => 'name',
        'field' => 'dishes.name',
        'modelKey' => 'custom',
    ]))->toBe('custom');
});

it('stamps modelKey onto a filter payload', function () {
    expect(FilterKey::withModelKey([
        'column' => 'name',
        'field' => 'dishes.name',
        'title' => 'Name',
    ]))->toMatchArray([
        'column' => 'name',
        'field' => 'dishes.name',
        'title' => 'Name',
        'modelKey' => 'name',
    ]);
});

it('builds live wire attributes from a filter definition', function () {
    $attributes = FilterWireAttributes::get('boolean', [
        'column' => 'name',
        'field' => 'dishes.name',
    ], 'Name');

    expect($attributes['selectAttributes']->get('wire:model.live'))->toBe('filters.name.value');
});

it('builds live wire attributes from a precomputed key string', function () {
    $attributes = FilterWireAttributes::get('boolean', 'in_stock', 'Stock');

    expect($attributes['selectAttributes']->get('wire:model.live'))->toBe('filters.in_stock.value');
});

it('builds deferred wire attributes from a filter definition', function () {
    $attributes = FilterWireAttributes::get('input_text', [
        'column' => 'name',
        'field' => 'dishes.name',
    ], 'Name', deferred: true);

    expect($attributes['inputAttributes']->get('wire:model'))->toBe('draftFilters.name.value')
        ->and($attributes['inputAttributes']->get('data-pg-draft'))->toBe('name.value')
        ->and($attributes['selectAttributes']->get('wire:model'))->toBe('draftFilters.name.op');
});

it('stamps view data so blades do not resolve keys', function () {
    $live = FilterWireAttributes::forView([
        'key' => 'boolean',
        'column' => 'name',
        'field' => 'dishes.name',
    ], deferred: false, title: 'Name');

    expect($live['modelKey'])->toBe('name')
        ->and($live['deferred'])->toBeFalse()
        ->and($live['filtersProperty'])->toBe('filters')
        ->and($live['selectAttributes']->get('wire:model.live'))->toBe('filters.name.value');

    $draft = FilterWireAttributes::forView([
        'key' => 'input_text',
        'column' => 'name',
        'field' => 'dishes.name',
    ], deferred: true, title: 'Name');

    expect($draft['filtersProperty'])->toBe('draftFilters')
        ->and($draft['inputAttributes']->get('wire:model'))->toBe('draftFilters.name.value')
        ->and($draft['selectAttributes']->get('wire:model'))->toBe('draftFilters.name.op');
});

it('stamps modelKey on the column when filters resolve', function () {
    Config::set('livewire-powergrid.filter', 'inline');

    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'filter-key-stamp';

        public function datasource()
        {
            return collect([
                ['id' => 1, 'name' => 'Dish'],
            ]);
        }

        public function filters(): array
        {
            return [
                Filter::inputText('name', 'dishes.name'),
                Filter::datepicker('produced_at'),
                Filter::boolean('in_stock'),
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
                Column::make('Date', 'produced_at'),
                Column::make('Stock', 'in_stock'),
            ];
        }
    };

    $test = Livewire::test($component::class);
    $instance = $test->instance();

    (new ReflectionMethod($instance, 'resolveFilters'))->invoke($instance);

    expect(data_get($instance->columns[0], 'filters.modelKey'))->toBe('name')
        ->and(data_get($instance->columns[0], 'filters.filtersProperty'))->toBe('filters')
        ->and(data_get($instance->columns[0], 'filters.inputAttributes')->get('wire:model.live.debounce.600ms'))->toBe('filters.name.value')
        ->and(data_get($instance->columns[1], 'filters.modelKey'))->toBe('produced_at')
        ->and(data_get($instance->columns[1], 'filters.deferred'))->toBeFalse()
        ->and(data_get($instance->columns[2], 'filters.modelKey'))->toBe('in_stock')
        ->and(data_get($instance->columns[2], 'filters.selectAttributes')->get('wire:model.live'))->toBe('filters.in_stock.value');

    expect($test->html())
        ->toContain('wire:model.live.debounce.600ms="filters.name.value"')
        ->not->toContain('dishes__pgdot__name')
        ->toContain('wire:model.live="filters.in_stock.value"')
        ->toContain('filters.produced_at.value.formatted');
});
