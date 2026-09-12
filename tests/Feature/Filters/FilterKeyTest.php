<?php

use Illuminate\Support\Facades\Config;
use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\{Column, PowerGridComponent, PowerGridFields};
use PowerComponents\LivewirePowerGrid\Facades\{Filter, PowerGrid};
use PowerComponents\LivewirePowerGrid\Support\{FilterKey, FilterWire};

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
    $wire = FilterWire::bags('boolean', [
        'column' => 'name',
        'field' => 'dishes.name',
    ]);

    expect($wire['value']->get('wire:model.live'))->toBe('filters.name.value');
});

it('builds live wire attributes from a precomputed key string', function () {
    $wire = FilterWire::bags('boolean', 'in_stock');

    expect($wire['value']->get('wire:model.live'))->toBe('filters.in_stock.value');
});

it('binds widget-owned controls without .live', function () {
    expect(FilterWire::bags('multi_select', 'category_id')['value']->get('wire:model'))
        ->toBe('filters.category_id.value')
        ->and(FilterWire::bags('date', 'created_at')['formatted']->get('wire:model'))
        ->toBe('filters.created_at.value.formatted');
});

it('rejects a filter type it has no bindings for', function () {
    FilterWire::bags('nope', 'name');
})->throws(InvalidArgumentException::class);

it('builds deferred wire attributes from a filter definition', function () {
    $wire = FilterWire::bags('input_text', [
        'column' => 'name',
        'field' => 'dishes.name',
    ], deferred: true);

    expect($wire['value']->get('wire:model'))->toBe('draftFilters.name.value')
        ->and($wire['value']->get('data-pg-draft'))->toBe('name.value')
        ->and($wire['operator']->get('wire:model'))->toBe('draftFilters.name.op');
});

it('overrides a slot binding through config', function () {
    Config::set('livewire-powergrid.filter_wire', ['input_text' => ['value' => 'live.debounce.800ms']]);

    $wire = FilterWire::bags('input_text', 'name');

    expect($wire['value']->get('wire:model.live.debounce.800ms'))->toBe('filters.name.value')
        ->and($wire['value']->has('wire:model.live.debounce.600ms'))->toBeFalse()
        ->and($wire['operator']->get('wire:model.live.debounce.600ms'))->toBe('filters.name.op');
});

it('lets the grid override the config binding', function () {
    Config::set('livewire-powergrid.filter_wire', ['select' => ['value' => 'blur']]);

    $wire = FilterWire::bags('select', 'category_id', overrides: ['select' => ['value' => 'live.throttle.500ms']]);

    expect($wire['value']->get('wire:model.live.throttle.500ms'))->toBe('filters.category_id.value');
});

it('binds a plain wire:model when the modifiers are empty', function () {
    expect(FilterWire::bags('boolean', 'in_stock', overrides: ['boolean' => ['value' => '']])['value']->get('wire:model'))
        ->toBe('filters.in_stock.value');
});

it('rejects an override for a slot that does not exist', function () {
    FilterWire::bags('input_text', 'name', overrides: ['input_text' => ['input' => 'live']]);
})->throws(InvalidArgumentException::class, 'Unknown filter slot [input]');

it('keeps the deferred binding out of reach of overrides', function () {
    Config::set('livewire-powergrid.filter_wire', ['input_text' => ['value' => 'live.debounce.800ms']]);

    $wire = FilterWire::bags('input_text', 'name', deferred: true);

    expect($wire['value']->get('wire:model'))->toBe('draftFilters.name.value')
        ->and($wire['value']->get('data-pg-draft'))->toBe('name.value');
});

it('stamps view data so blades do not resolve keys', function () {
    $live = FilterWire::forView([
        'key' => 'boolean',
        'column' => 'name',
        'field' => 'dishes.name',
    ], deferred: false);

    expect($live['modelKey'])->toBe('name')
        ->and($live['deferred'])->toBeFalse()
        ->and($live['filtersProperty'])->toBe('filters')
        ->and($live['wire']['value']->get('wire:model.live'))->toBe('filters.name.value');

    $draft = FilterWire::forView([
        'key' => 'input_text',
        'column' => 'name',
        'field' => 'dishes.name',
    ], deferred: true);

    expect($draft['filtersProperty'])->toBe('draftFilters')
        ->and($draft['wire']['value']->get('wire:model'))->toBe('draftFilters.name.value')
        ->and($draft['wire']['operator']->get('wire:model'))->toBe('draftFilters.name.op');
});

it('renders the grid override on the filter input', function () {
    Config::set('livewire-powergrid.filter', 'inline');

    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'filter-wire-override';

        public function filterWire(): array
        {
            return ['input_text' => ['value' => 'live.debounce.800ms']];
        }

        public function datasource()
        {
            return collect([['id' => 1, 'name' => 'Dish']]);
        }

        public function filters(): array
        {
            return [Filter::inputText('name')];
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

    expect(Livewire::test($component::class)->html())
        ->toContain('wire:model.live.debounce.800ms="filters.name.value"')
        ->not->toContain('wire:model.live.debounce.600ms="filters.name.value"');
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
        ->and(data_get($instance->columns[0], 'filters.wire.value')->get('wire:model.live.debounce.600ms'))->toBe('filters.name.value')
        ->and(data_get($instance->columns[1], 'filters.modelKey'))->toBe('produced_at')
        ->and(data_get($instance->columns[1], 'filters.deferred'))->toBeFalse()
        ->and(data_get($instance->columns[1], 'filters.wire.formatted')->get('wire:model'))->toBe('filters.produced_at.value.formatted')
        ->and(data_get($instance->columns[2], 'filters.modelKey'))->toBe('in_stock')
        ->and(data_get($instance->columns[2], 'filters.wire.value')->get('wire:model.live'))->toBe('filters.in_stock.value');

    expect($test->html())
        ->toContain('wire:model.live.debounce.600ms="filters.name.value"')
        ->not->toContain('dishes__pgdot__name')
        ->toContain('wire:model.live="filters.in_stock.value"')
        ->toContain('wire:model="filters.produced_at.value.formatted"');
});
