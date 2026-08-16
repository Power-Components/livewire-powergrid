<?php

use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\{Column, PowerGridComponent, PowerGridFields};
use PowerComponents\LivewirePowerGrid\Facades\{Filter, PowerGrid};

uses()->group('filters', 'enabled-filters');

function lifecycleComponent(string $tableName): PowerGridComponent
{
    return new class($tableName) extends PowerGridComponent
    {
        public function __construct(public string $tableName = 'enabled-filters-lifecycle') {}

        public function datasource()
        {
            return collect([
                ['id' => 1, 'name' => 'Cheap Dish', 'price' => 10, 'in_stock' => true],
                ['id' => 2, 'name' => 'Mid Dish', 'price' => 50, 'in_stock' => true],
                ['id' => 3, 'name' => 'Expensive Dish', 'price' => 500, 'in_stock' => false],
            ]);
        }

        public function filters(): array
        {
            return [
                Filter::inputText('name'),
                Filter::number('price'),
                Filter::boolean('in_stock'),
            ];
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name')->add('price')->add('in_stock');
        }

        public function columns(): array
        {
            return [Column::make('Name', 'name')];
        }
    };
}

it('accumulates enabled filters with their labels and renders them as badges', function () {
    $test = Livewire::test(lifecycleComponent('enabled-accumulate')::class)
        ->call('filterInputText', 'name', 'Dish', 'Dish Name')
        ->call('filterBoolean', 'in_stock', 'true', 'In Stock');

    $labels = collect($test->get('enabledFilters'))->pluck('label')->all();

    expect($test->get('enabledFilters'))->toHaveCount(2)
        ->and($labels)->toContain('Dish Name')
        ->and($labels)->toContain('In Stock');

    // Badges are rendered in the header
    $test->assertSee('Dish Name')
        ->assertSee('In Stock');
});

it('removes a single enabled filter with clearFilter and keeps the others', function () {
    $test = Livewire::test(lifecycleComponent('enabled-clear-one')::class)
        ->call('filterInputText', 'name', 'Dish', 'Dish Name')
        ->call('filterBoolean', 'in_stock', 'true', 'In Stock')
        ->call('clearFilter', 'name');

    $labels = collect($test->get('enabledFilters'))->pluck('label')->all();

    expect($test->get('enabledFilters'))->toHaveCount(1)
        ->and($labels)->toContain('In Stock')
        ->and($labels)->not->toContain('Dish Name');
});

it('removes every enabled filter with clearAllFilters', function () {
    $test = Livewire::test(lifecycleComponent('enabled-clear-all')::class)
        ->call('filterInputText', 'name', 'Dish', 'Dish Name')
        ->call('filterBoolean', 'in_stock', 'true', 'In Stock')
        ->call('clearAllFilters');

    expect($test->get('enabledFilters'))->toBeEmpty()
        ->and($test->get('filters'))->toBeEmpty();
});

it('does not add duplicate enabled filters for the same field', function () {
    $test = Livewire::test(lifecycleComponent('enabled-dedup')::class)
        ->call('filterInputText', 'name', 'Cheap', 'Dish Name')
        ->call('filterInputText', 'name', 'Mid', 'Dish Name');

    expect($test->get('enabledFilters'))->toHaveCount(1);
});

it('clearFilter strips the _start/_end suffix and clears the whole number range', function () {
    $test = Livewire::test(lifecycleComponent('clear-number-range')::class)
        ->set('filters.number.price.start', '15')
        ->set('filters.number.price.end', '100')
        ->assertSee('Mid Dish')
        ->assertDontSee('Cheap Dish')
        ->assertDontSee('Expensive Dish')

        // The badge for a number filter clears via the "<field>_start" key
        ->call('clearFilter', 'price_start');

    expect(data_get($test->get('filters'), 'number.price'))->toBeNull();

    $test->assertSee('Cheap Dish')
        ->assertSee('Mid Dish')
        ->assertSee('Expensive Dish');
});
