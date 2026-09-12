<?php

use Illuminate\Support\Facades\Config;
use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\{Column, PowerGridComponent, PowerGridFields};
use PowerComponents\LivewirePowerGrid\Facades\{Filter, PowerGrid};

uses()->group('filters', 'filter-dropdown');

function dropdownComponent(string $tableName): PowerGridComponent
{
    return new class($tableName) extends PowerGridComponent
    {
        public function __construct(public string $tableName = 'filter-dropdown') {}

        public function datasource()
        {
            return collect([
                ['id' => 1, 'name' => 'Cheap Dish', 'price' => 10, 'in_stock' => true, 'created_at' => '2026-01-10 00:00:00'],
                ['id' => 2, 'name' => 'Mid Dish', 'price' => 50, 'in_stock' => true, 'created_at' => '2026-02-10 00:00:00'],
                ['id' => 3, 'name' => 'Expensive Dish', 'price' => 500, 'in_stock' => false, 'created_at' => '2026-03-10 00:00:00'],
            ]);
        }

        public function filters(): array
        {
            return [
                Filter::inputText('name'),
                Filter::number('price'),
                Filter::boolean('in_stock'),
                Filter::datetimepicker('created_at'),
            ];
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()
                ->add('id')->add('name')->add('price')->add('in_stock')->add('created_at');
        }

        public function columns(): array
        {
            return [
                Column::make('Name', 'name'),
                Column::make('Price', 'price'),
                Column::make('In Stock', 'in_stock'),
                Column::make('Created At', 'created_at'),
            ];
        }
    };
}

function dropdownMultiSelectComponent(string $tableName): PowerGridComponent
{
    return new class($tableName) extends PowerGridComponent
    {
        public function __construct(public string $tableName = 'filter-dropdown-multi') {}

        public function datasource()
        {
            return collect([
                ['id' => 1, 'name' => 'Cheap Dish', 'price' => 10, 'category_id' => 1],
                ['id' => 2, 'name' => 'Mid Dish', 'price' => 50, 'category_id' => 2],
                ['id' => 3, 'name' => 'Expensive Dish', 'price' => 500, 'category_id' => 3],
            ]);
        }

        public function filters(): array
        {
            return [
                Filter::inputText('name'),
                Filter::number('price'),
                Filter::multiSelect('category_id')
                    ->dataSource(collect([
                        ['id' => 1, 'name' => 'Cat 1'],
                        ['id' => 2, 'name' => 'Cat 2'],
                        ['id' => 3, 'name' => 'Cat 3'],
                    ]))
                    ->optionValue('id')
                    ->optionLabel('name'),
            ];
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name')->add('price')->add('category_id');
        }

        public function columns(): array
        {
            return [
                Column::make('Name', 'name'),
                Column::make('Price', 'price'),
                Column::make('Category', 'category_id'),
            ];
        }
    };
}

beforeEach(fn () => Config::set('livewire-powergrid.filter', 'dropdown'));

it('does not filter until Apply is pressed', function () {
    Livewire::test(dropdownComponent('dropdown-defer')::class)
        ->set('draftFilters.name.value', 'Expensive')
        ->assertSet('filters', [])
        ->assertSee('Cheap Dish')
        ->assertSee('Mid Dish')
        ->assertSee('Expensive Dish');
});

it('commits the draft filters on applyFilters', function () {
    $test = Livewire::test(dropdownComponent('dropdown-apply')::class)
        ->set('draftFilters.name.value', 'Expensive')
        ->call('applyFilters');

    expect($test->get('filters'))->toMatchArray(['name' => ['type' => 'input_text', 'value' => 'Expensive']])
        ->and($test->get('enabledFilters'))->toHaveCount(1);

    $test->assertSee('Expensive Dish')
        ->assertDontSee('Cheap Dish');
});

it('commits a draft payload passed directly to applyFilters', function () {
    $test = Livewire::test(dropdownComponent('dropdown-apply-arg')::class)
        ->call('applyFilters', ['name' => ['type' => 'input_text', 'value' => 'Expensive']]);

    expect($test->get('filters'))->toMatchArray(['name' => ['type' => 'input_text', 'value' => 'Expensive']]);

    $test->assertSee('Expensive Dish')
        ->assertDontSee('Cheap Dish');
});

it('commits a field-keyed DOM snapshot without a prior draftFilters write', function () {
    $test = Livewire::test(dropdownComponent('dropdown-apply-dom')::class)
        ->call('applyFilters', [
            'name' => ['value' => 'Expensive', 'op' => 'contains'],
            'price' => ['value' => ['start' => '', 'end' => '']],
            'in_stock' => ['value' => 'all'],
        ]);

    expect($test->get('filters'))->toMatchArray(['name' => ['type' => 'input_text', 'value' => 'Expensive', 'op' => 'contains']])
        ->and($test->get('draftFilters.name.value'))->toBe('Expensive');

    $test->assertSee('Expensive Dish')
        ->assertDontSee('Cheap Dish');
});

it('commits a number max from a DOM snapshot without a prior draftFilters write', function () {
    $test = Livewire::test(dropdownComponent('dropdown-apply-number-end')::class)
        ->call('applyFilters', [
            'price' => ['value' => ['start' => '', 'end' => '50']],
        ]);

    expect($test->get('filters.price.value.end'))->toBe('50')
        ->and($test->get('filters.price.value'))->not->toHaveKey('start');

    $test->assertSee('Cheap Dish')
        ->assertSee('Mid Dish')
        ->assertDontSee('Expensive Dish');
});

it('binds multi_select to the unified field-keyed draft path', function () {
    $html = Livewire::test(dropdownMultiSelectComponent('dropdown-multi-html')::class)
        ->call('loadFilterPanel')
        ->html();

    expect($html)->toContain('wire:model="draftFilters.category_id.value"')
        ->and($html)->toContain('data-pg-draft="category_id.value"')
        ->and($html)->not->toContain('draftFilters.multi_select.')
        ->and($html)->not->toContain('data-pg-draft="multi_select.');
});

it('keeps sibling filters when the DOM snapshot carries a multi_select field', function () {
    $test = Livewire::test(dropdownMultiSelectComponent('dropdown-multi-apply')::class)
        ->call('applyFilters', [
            'name' => ['value' => '', 'op' => 'contains'],
            'price' => ['value' => ['start' => '', 'end' => '50']],
            'category_id' => ['value' => []],
        ]);

    expect($test->get('filters.price.value.end'))->toBe('50');

    $test->assertSee('Cheap Dish')
        ->assertSee('Mid Dish')
        ->assertDontSee('Expensive Dish');
});

it('reset restores the draft to the applied filters and keeps results', function () {
    $test = Livewire::test(dropdownComponent('dropdown-reset')::class)
        ->set('draftFilters.name.value', 'Expensive')
        ->call('applyFilters')
        ->set('draftFilters.name.value', 'Cheap')
        ->call('resetFilters');

    expect($test->get('draftFilters'))->toMatchArray(['name' => ['type' => 'input_text', 'value' => 'Expensive']])
        ->and($test->get('filters'))->toMatchArray(['name' => ['type' => 'input_text', 'value' => 'Expensive']]);

    $test->assertSee('Expensive Dish')
        ->assertDontSee('Cheap Dish');
});

it('clearAllFilters wipes applied filters, the draft and enabled filters', function () {
    $test = Livewire::test(dropdownComponent('dropdown-clear')::class)
        ->set('draftFilters.name.value', 'Expensive')
        ->call('applyFilters')
        ->call('clearAllFilters');

    expect($test->get('filters'))->toBeEmpty()
        ->and($test->get('draftFilters'))->toBeEmpty()
        ->and($test->get('enabledFilters'))->toBeEmpty();

    $test->assertSee('Cheap Dish')->assertSee('Expensive Dish');
});

it('counts distinct applied filters, collapsing a number range to one', function () {
    $test = Livewire::test(dropdownComponent('dropdown-count')::class)
        ->set('draftFilters.name.value', 'Dish')
        ->set('draftFilters.price.value.start', '1')
        ->set('draftFilters.price.value.end', '100')
        ->set('draftFilters.in_stock.value', 'true')
        ->call('applyFilters');

    expect($test->instance()->activeFilterCount())->toBe(3);
});

it('does not count blank draft values as applied filters', function () {
    $test = Livewire::test(dropdownComponent('dropdown-blank')::class)
        ->set('draftFilters.name.value', '')
        ->call('applyFilters');

    expect($test->get('filters'))->toBeEmpty()
        ->and($test->instance()->activeFilterCount())->toBe(0);
});

it('derives flatpickr start/end from the draft formatted string on apply', function () {
    $test = Livewire::test(dropdownComponent('dropdown-date')::class)
        ->set('draftFilters.created_at.value.formatted', '2026-01-01 to 2026-01-31')
        ->call('applyFilters');

    $range = $test->get('filters.created_at.value');

    expect($range)->toHaveKeys(['start', 'end', 'formatted'])
        ->and($range['formatted'])->toBe('2026-01-01 to 2026-01-31');

    $test->assertSee('Cheap Dish')
        ->assertDontSee('Expensive Dish');
});

it('does not mount filter fields until the panel is loaded', function () {
    $html = Livewire::test(dropdownComponent('dropdown-lazy')::class)->html();

    expect($html)->toContain('data-cy="filter-dropdown-apply"')
        ->and($html)->not->toContain('draftFilters.name.value');
});

it('renders deferred draftFilters bindings and no live handler in dropdown mode', function () {
    $test = Livewire::test(dropdownComponent('dropdown-html')::class)
        ->call('loadFilterPanel');

    $fragments = \Livewire\store($test->instance())->get('partialFragments') ?? [];

    $names = [];

    foreach ($fragments as $renderUsing) {
        $names = array_merge($names, array_keys($renderUsing()));
    }

    expect($names)->toContain('pg-filter-fields-'.$test->instance()->tableName)
        ->and($names)->not->toContain('pg-filters-'.$test->instance()->tableName)
        ->and($test->html())->toContain('draftFilters.name.value')
        ->and($test->html())->toContain('data-pg-draft="name.value"')
        ->and($test->html())->toContain('data-pg-draft="price.value.start"')
        ->and($test->html())->toContain('data-pg-draft="price.value.end"')
        ->and($test->html())->toContain('data-pg-draft="in_stock.value"')
        ->and($test->html())->toContain('data-cy="filter-dropdown-apply"')
        ->and($test->html())->not->toContain('wire:input.live.debounce.600ms="filterInputText');
});

it('closes apply and clear through alpine before the livewire snapshot', function () {
    $html = Livewire::test(dropdownComponent('dropdown-alpine-apply')::class)->html();

    expect($html)->toContain('x-on:pointerdown.prevent="apply()"')
        ->and($html)->toContain('x-on:click="if ($event.detail === 0) apply()"')
        ->and($html)->toContain('x-on:click="clearAll()"')
        ->and($html)->not->toContain('wire:click.prevent="applyFilters"')
        ->and($html)->not->toContain('wire:click.prevent="clearAllFilters"');
});

it('ignores portaled date and select widgets on click outside', function () {
    $html = Livewire::test(dropdownComponent('dropdown-outside')::class)->html();

    expect($html)->toContain('closeOnOutside($event)');
});

it('pins the panel to the viewport on small screens', function () {
    $html = Livewire::test(dropdownComponent('dropdown-panel')::class)->html();

    expect($html)->toContain('max-w-[calc(100vw-2rem)]')
        ->and($html)->toContain('fixed inset-x-4')
        ->and($html)->not->toContain('w-[90vw]');
});

it('uses a single grid column for four or fewer filters', function () {
    $test = Livewire::test(dropdownComponent('dropdown-grid')::class);

    expect($test->instance()->filterPanelColumns())->toBe(1)
        ->and($test->html())->toContain('grid grid-cols-1 gap-4');
});

it('keeps the inline flow live and bound to filters', function () {
    Config::set('livewire-powergrid.filter', 'inline');

    $html = Livewire::test(dropdownComponent('dropdown-inline')::class)->html();

    expect($html)->toContain('wire:model.live.debounce.600ms="filters.name.value"')
        ->and($html)->not->toContain('wire:input.live.debounce.600ms="filterInputText')
        ->and($html)->not->toContain('draftFilters.name.value');
});

it('registers tbody and pagination partials when applying an inline filter', function () {
    Config::set('livewire-powergrid.filter', 'inline');

    $test = Livewire::test(dropdownComponent('inline-partials')::class)
        ->set('filters.name.value', 'Expensive');

    $fragments = \Livewire\store($test->instance())->get('partialFragments') ?? [];

    $names = [];

    foreach ($fragments as $renderUsing) {
        $names = array_merge($names, array_keys($renderUsing()));
    }

    expect($names)
        ->toContain('pg-tbody-'.$test->instance()->tableName)
        ->toContain('pg-pagination-'.$test->instance()->tableName);
});
