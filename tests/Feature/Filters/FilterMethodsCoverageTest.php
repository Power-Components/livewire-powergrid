<?php

use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\{Column, Facades\PowerGrid, PowerGridComponent, PowerGridFields};
use PowerComponents\LivewirePowerGrid\Facades\Filter;

uses()->group('filters');

function filterMethodsComponent(string $tableName): PowerGridComponent
{
    return new class($tableName) extends PowerGridComponent
    {
        public function __construct(public string $tableName = 'filter-methods') {}

        public function datasource()
        {
            return collect([
                ['id' => 1, 'name' => 'Cheap', 'price' => 10, 'category_id' => 1, 'in_stock' => true],
                ['id' => 2, 'name' => 'Mid', 'price' => 50, 'category_id' => 2, 'in_stock' => true],
                ['id' => 3, 'name' => 'Expensive', 'price' => 500, 'category_id' => 1, 'in_stock' => false],
            ]);
        }

        public function filters(): array
        {
            return [
                Filter::inputText('name'),
                Filter::number('price'),
                Filter::boolean('in_stock'),
                Filter::select('category_id')
                    ->dataSource(collect([
                        ['category_id' => 1, 'name' => 'Cat 1'],
                        ['category_id' => 2, 'name' => 'Cat 2'],
                    ]))
                    ->optionValue('category_id')
                    ->optionLabel('name'),
                Filter::multiSelect('category_id')
                    ->dataSource(collect([
                        ['category_id' => 1, 'name' => 'Cat 1'],
                        ['category_id' => 2, 'name' => 'Cat 2'],
                    ]))
                    ->optionValue('category_id')
                    ->optionLabel('name'),
            ];
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name')->add('price');
        }

        public function columns(): array
        {
            return [Column::make('Name', 'name')];
        }
    };
}

it('applies a select filter through the filterSelect method', function () {
    $test = Livewire::test(filterMethodsComponent('m-select')::class)
        ->set('filters.select.category_id', '2')
        ->call('filterSelect', 'category_id', 'Category');

    expect(collect($test->get('enabledFilters'))->pluck('field'))->toContain('category_id');

    $test->assertSee('Mid')
        ->assertDontSee('Cheap');
});

it('clears a select filter through filterSelect when the value is blank', function () {
    $test = Livewire::test(filterMethodsComponent('m-select-blank')::class)
        ->set('filters.select.category_id', '')
        ->call('filterSelect', 'category_id', 'Category');

    expect($test->get('enabledFilters'))->toBeEmpty();
});

it('applies a number range through filterNumberStart and filterNumberEnd', function () {
    $test = Livewire::test(filterMethodsComponent('m-number')::class)
        ->set('filters.number.price.start', '15')
        ->call('filterNumberStart', 'price', ['title' => 'Price'], '15')
        ->set('filters.number.price.end', '100')
        ->call('filterNumberEnd', 'price', ['title' => 'Price'], '100');

    expect(collect($test->get('enabledFilters'))->pluck('field'))->toContain('price');

    $test->assertSee('Mid')
        ->assertDontSee('Cheap')
        ->assertDontSee('Expensive');
});

it('clears the number filter through filterNumberStart when the value is blank', function () {
    $test = Livewire::test(filterMethodsComponent('m-number-blank')::class)
        ->call('filterNumberStart', 'price', ['title' => 'Price'], '');

    expect(collect($test->get('enabledFilters'))->where('field', 'price'))->toBeEmpty();
});

it('sets the operator and disables the input for nullability operators via filterInputTextOptions', function () {
    $test = Livewire::test(filterMethodsComponent('m-options')::class)
        ->set('filters.input_text.name', 'ignored')
        ->call('filterInputTextOptions', 'name', 'is_empty', 'Name');

    // input value is wiped for a nullability operator, and the enabled filter is disabled
    expect(data_get($test->get('filters'), 'input_text.name'))->toBeNull()
        ->and(data_get($test->get('filters'), 'input_text_options.name'))->toBe('is_empty');

    $enabled = collect($test->get('enabledFilters'))->firstWhere('field', 'name');
    expect($enabled['disabled'])->toBeTrue();
});

it('keeps the input enabled for a regular operator via filterInputTextOptions', function () {
    $test = Livewire::test(filterMethodsComponent('m-options-regular')::class)
        ->call('filterInputTextOptions', 'name', 'contains', 'Name');

    expect(data_get($test->get('filters'), 'input_text_options.name'))->toBe('contains');

    $enabled = collect($test->get('enabledFilters'))->firstWhere('field', 'name');
    expect($enabled['disabled'])->toBeFalse();
});

it('applies a multi select filter through multiSelectChanged', function () {
    $test = Livewire::test(filterMethodsComponent('m-multi')::class)
        ->call('multiSelectChanged', 'category_id', 'Category', ['1']);

    expect(collect($test->get('enabledFilters'))->pluck('field'))->toContain('category_id')
        ->and(data_get($test->get('filters'), 'multi_select.category_id'))->toBe(['1']);
});

it('clears the multi select filter through multiSelectChanged with an empty selection', function () {
    $test = Livewire::test(filterMethodsComponent('m-multi-empty')::class)
        ->call('multiSelectChanged', 'category_id', 'Category', ['1'])
        ->call('multiSelectChanged', 'category_id', 'Category', []);

    expect(collect($test->get('enabledFilters'))->where('field', 'category_id'))->toBeEmpty();
});

it('applies default filter values on mount for every filter type', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'm-defaults';

        public function datasource()
        {
            return collect([
                ['id' => 1, 'name' => 'Cheap', 'price' => 10, 'category_id' => 1, 'in_stock' => true],
                ['id' => 2, 'name' => 'Mid', 'price' => 50, 'category_id' => 2, 'in_stock' => false],
            ]);
        }

        public function filters(): array
        {
            return [
                Filter::inputText('name')->default('Cheap'),
                Filter::number('price')->default(['start' => 5, 'end' => 20]),
                Filter::boolean('in_stock')->default('true'),
                Filter::select('category_id')
                    ->dataSource(collect([['category_id' => 1, 'name' => 'Cat 1']]))
                    ->optionValue('category_id')->optionLabel('name')
                    ->default(1),
                Filter::multiSelect('category_id', 'categories')
                    ->dataSource(collect([['category_id' => 1, 'name' => 'Cat 1']]))
                    ->optionValue('category_id')->optionLabel('name')
                    ->default([1]),
            ];
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name')->add('price');
        }

        public function columns(): array
        {
            return [Column::make('Name', 'name')];
        }
    };

    $test = Livewire::test($component::class);

    $fields = collect($test->get('enabledFilters'))->pluck('field');

    expect($fields)->toContain('name')
        ->and($fields)->toContain('price')
        ->and($fields)->toContain('in_stock')
        ->and($fields)->toContain('category_id');
});
