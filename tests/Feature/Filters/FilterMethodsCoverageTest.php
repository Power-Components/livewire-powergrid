<?php

use Illuminate\Support\Facades\Config;
use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\{Column, PowerGridComponent, PowerGridFields};
use PowerComponents\LivewirePowerGrid\Facades\{Filter, PowerGrid};

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

it('commits an inline filter when Livewire updates the filters bag', function () {
    $test = Livewire::test(filterMethodsComponent('m-updated')::class)
        ->set('filters.name.value', 'Mid');

    expect(collect($test->get('enabledFilters'))->pluck('field'))->toContain('name');

    $test->assertSee('Mid')
        ->assertDontSee('Cheap')
        ->assertDontSee('Expensive');
});

it('applies a select filter when the bag is written', function () {
    $test = Livewire::test(filterMethodsComponent('m-select')::class)
        ->set('filters.category_id.value', '2');

    expect(collect($test->get('enabledFilters'))->pluck('field'))->toContain('category_id');

    $test->assertSee('Mid')
        ->assertDontSee('Cheap');
});

it('clears a select filter when the value is blank', function () {
    $test = Livewire::test(filterMethodsComponent('m-select-blank')::class)
        ->set('filters.category_id.value', '');

    expect($test->get('enabledFilters'))->toBeEmpty();
});

it('applies a number range through the value bounds', function () {
    $test = Livewire::test(filterMethodsComponent('m-number')::class)
        ->set('filters.price.value.start', '15')
        ->set('filters.price.value.end', '100');

    expect(collect($test->get('enabledFilters'))->pluck('field'))->toContain('price');

    $test->assertSee('Mid')
        ->assertDontSee('Cheap')
        ->assertDontSee('Expensive');
});

it('clears the number filter when the bound is blank', function () {
    $test = Livewire::test(filterMethodsComponent('m-number-blank')::class)
        ->set('filters.price.value.start', '');

    expect(collect($test->get('enabledFilters'))->where('field', 'price'))->toBeEmpty();
});

it('sets the operator and disables the input for nullability operators', function () {
    $test = Livewire::test(filterMethodsComponent('m-options')::class)
        ->set('filters.name.value', 'ignored')
        ->set('filters.name.op', 'is_empty');

    // input value is wiped for a nullability operator, and the enabled filter is disabled
    expect(data_get($test->get('filters'), 'name.value'))->toBeNull()
        ->and(data_get($test->get('filters'), 'name.op'))->toBe('is_empty');

    $enabled = collect($test->get('enabledFilters'))->firstWhere('field', 'name');
    expect($enabled['disabled'])->toBeTrue();
});

it('remembers a regular operator outside the value bag until the value is filled', function () {
    $test = Livewire::test(filterMethodsComponent('m-options-regular')::class)
        ->set('filters.name.op', 'contains');

    expect($test->get('filters'))->not->toHaveKey('name')
        ->and(data_get($test->get('filterOperators'), 'name'))->toBe('contains')
        ->and(data_get($test->get('draftFilters'), 'name.op'))->toBe('contains')
        ->and(collect($test->get('enabledFilters'))->firstWhere('field', 'name'))->toBeNull();

    $test->set('filters.name.value', 'Mid');

    expect(data_get($test->get('filters'), 'name'))
        ->toMatchArray(['type' => 'input_text', 'value' => 'Mid', 'op' => 'contains']);
});

it('applies a multi select filter through multiSelectChanged', function () {
    $test = Livewire::test(filterMethodsComponent('m-multi')::class)
        ->call('multiSelectChanged', 'category_id', 'Category', ['1']);

    expect(collect($test->get('enabledFilters'))->pluck('field'))->toContain('category_id')
        ->and(data_get($test->get('filters'), 'category_id.value'))->toBe(['1']);
});

it('clears the multi select filter through multiSelectChanged with an empty selection', function () {
    $test = Livewire::test(filterMethodsComponent('m-multi-empty')::class)
        ->call('multiSelectChanged', 'category_id', 'Category', ['1'])
        ->call('multiSelectChanged', 'category_id', 'Category', []);

    $test->assertDispatched('pg:clear_multi_select::filter-methods:category_id');

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

it('calls filters() once per request', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'filters-once';

        public int $filterCalls = 0;

        public function datasource()
        {
            return collect([['id' => 1, 'name' => 'Dish']]);
        }

        public function filters(): array
        {
            $this->filterCalls++;

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

    $test = Livewire::test($component::class);

    expect($test->instance()->filterCalls)->toBe(1)
        ->and($test->html())->toContain('Dish');
});

it('does not instantiate filters() when morphing dropdown grid partials', function () {
    Config::set('livewire-powergrid.filter', 'dropdown');

    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'filters-sort-once';

        public static int $filterCalls = 0;

        public function datasource()
        {
            return collect([['id' => 1, 'name' => 'Zebra'], ['id' => 2, 'name' => 'Apple']]);
        }

        public function filters(): array
        {
            self::$filterCalls++;

            return [Filter::inputText('name')];
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name');
        }

        public function columns(): array
        {
            return [Column::make('Name', 'name')->sortable()];
        }
    };

    $test = Livewire::test($component::class);
    $instance = $test->instance();

    $component::$filterCalls = 0;

    Closure::bind(function (): void {
        $this->plugins = [];
    }, $instance, PowerGridComponent::class)();

    Closure::bind(function (): void {
        $this->declaredFiltersCache = null;
    }, $instance, $instance)();

    $instance->renderGridPartials(includeThead: true);

    expect($component::$filterCalls)->toBe(0);
});
