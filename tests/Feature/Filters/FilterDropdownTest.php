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

beforeEach(fn () => Config::set('livewire-powergrid.filter', 'dropdown'));

it('does not filter until Apply is pressed', function () {
    Livewire::test(dropdownComponent('dropdown-defer')::class)
        ->set('draftFilters.input_text.name', 'Expensive')
        ->assertSet('filters', [])
        ->assertSee('Cheap Dish')
        ->assertSee('Mid Dish')
        ->assertSee('Expensive Dish');
});

it('commits the draft filters on applyFilters', function () {
    $test = Livewire::test(dropdownComponent('dropdown-apply')::class)
        ->set('draftFilters.input_text.name', 'Expensive')
        ->call('applyFilters');

    expect($test->get('filters'))->toBe(['input_text' => ['name' => 'Expensive']])
        ->and($test->get('enabledFilters'))->toHaveCount(1);

    $test->assertSee('Expensive Dish')
        ->assertDontSee('Cheap Dish');
});

it('commits a draft payload passed directly to applyFilters', function () {
    $test = Livewire::test(dropdownComponent('dropdown-apply-arg')::class)
        ->call('applyFilters', ['input_text' => ['name' => 'Expensive']]);

    expect($test->get('filters'))->toBe(['input_text' => ['name' => 'Expensive']]);

    $test->assertSee('Expensive Dish')
        ->assertDontSee('Cheap Dish');
});

it('reset restores the draft to the applied filters and keeps results', function () {
    $test = Livewire::test(dropdownComponent('dropdown-reset')::class)
        ->set('draftFilters.input_text.name', 'Expensive')
        ->call('applyFilters')
        ->set('draftFilters.input_text.name', 'Cheap')
        ->call('resetFilters');

    expect($test->get('draftFilters'))->toBe(['input_text' => ['name' => 'Expensive']])
        ->and($test->get('filters'))->toBe(['input_text' => ['name' => 'Expensive']]);

    $test->assertSee('Expensive Dish')
        ->assertDontSee('Cheap Dish');
});

it('clearAllFilters wipes applied filters, the draft and enabled filters', function () {
    $test = Livewire::test(dropdownComponent('dropdown-clear')::class)
        ->set('draftFilters.input_text.name', 'Expensive')
        ->call('applyFilters')
        ->call('clearAllFilters');

    expect($test->get('filters'))->toBeEmpty()
        ->and($test->get('draftFilters'))->toBeEmpty()
        ->and($test->get('enabledFilters'))->toBeEmpty();

    $test->assertSee('Cheap Dish')->assertSee('Expensive Dish');
});

it('counts distinct applied filters, collapsing a number range to one', function () {
    $test = Livewire::test(dropdownComponent('dropdown-count')::class)
        ->set('draftFilters.input_text.name', 'Dish')
        ->set('draftFilters.number.price.start', '1')
        ->set('draftFilters.number.price.end', '100')
        ->set('draftFilters.boolean.in_stock', 'true')
        ->call('applyFilters');

    expect($test->instance()->activeFilterCount())->toBe(3);
});

it('does not count blank draft values as applied filters', function () {
    $test = Livewire::test(dropdownComponent('dropdown-blank')::class)
        ->set('draftFilters.input_text.name', '')
        ->call('applyFilters');

    expect($test->get('filters'))->toBeEmpty()
        ->and($test->instance()->activeFilterCount())->toBe(0);
});

it('derives flatpickr start/end from the draft formatted string on apply', function () {
    $test = Livewire::test(dropdownComponent('dropdown-date')::class)
        ->set('draftFilters.datetime.created_at.formatted', '2026-01-01 to 2026-01-31')
        ->call('applyFilters');

    $range = $test->get('filters.datetime.created_at');

    expect($range)->toHaveKeys(['start', 'end', 'formatted'])
        ->and($range['formatted'])->toBe('2026-01-01 to 2026-01-31');

    $test->assertSee('Cheap Dish')
        ->assertDontSee('Expensive Dish');
});

it('does not mount filter fields until the panel is loaded', function () {
    $html = Livewire::test(dropdownComponent('dropdown-lazy')::class)->html();

    expect($html)->toContain('data-cy="filter-dropdown-apply"')
        ->and($html)->not->toContain('draftFilters.input_text.name');
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
        ->and($test->html())->toContain('draftFilters.input_text.name')
        ->and($test->html())->toContain('data-pg-draft="input_text.name"')
        ->and($test->html())->toContain('data-cy="filter-dropdown-apply"')
        ->and($test->html())->not->toContain('wire:input.live.debounce.600ms="filterInputText');
});

it('closes apply and clear through alpine before the livewire snapshot', function () {
    $html = Livewire::test(dropdownComponent('dropdown-alpine-apply')::class)->html();

    expect($html)->toContain('x-on:click="apply()"')
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

    expect($html)->toContain('filters.input_text.name')
        ->and($html)->toContain('wire:input.live.debounce.600ms="filterInputText')
        ->and($html)->not->toContain('draftFilters.input_text.name');
});

it('registers tbody and pagination partials when applying an inline filter', function () {
    Config::set('livewire-powergrid.filter', 'inline');

    $test = Livewire::test(dropdownComponent('inline-partials')::class)
        ->call('filterInputText', 'name', 'Expensive', 'Name');

    $fragments = \Livewire\store($test->instance())->get('partialFragments') ?? [];

    $names = [];

    foreach ($fragments as $renderUsing) {
        $names = array_merge($names, array_keys($renderUsing()));
    }

    expect($names)
        ->toContain('pg-tbody-'.$test->instance()->tableName)
        ->toContain('pg-pagination-'.$test->instance()->tableName);
});
