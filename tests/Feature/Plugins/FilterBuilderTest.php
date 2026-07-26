<?php

use Livewire\Livewire;
use PowerComponents\LivewirePowerGrid\{Column, Facades\Filter, Facades\PowerGrid, PowerGridComponent, PowerGridFields};
use PowerComponents\LivewirePowerGrid\Plugins\FilterBuilder\FilterBuilderPlugin;
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Models\Dish;
use PowerComponents\LivewirePowerGrid\Tests\Concerns\TestDatabase;
use PowerComponents\LivewirePowerGrid\Themes\{Flux, Tailwind};

uses()->group('filters', 'filter-builder');

function filterBuilderCollectionComponent(): PowerGridComponent
{
    return new class() extends PowerGridComponent
    {
        public string $tableName = 'fb-collection';

        public function setUp(): array
        {
            return [PowerGrid::filterBuilder()];
        }

        public function datasource()
        {
            return collect([
                ['id' => 1, 'name' => 'Pastel', 'price' => 10.00, 'active' => true],
                ['id' => 2, 'name' => 'Carne', 'price' => 30.00, 'active' => true],
                ['id' => 3, 'name' => 'Sushi', 'price' => 60.00, 'active' => false],
                ['id' => 4, 'name' => 'Torta', 'price' => 50.00, 'active' => false],
            ]);
        }

        public function filters(): array
        {
            return [
                Filter::inputText('name'),
                Filter::number('price'),
                Filter::boolean('active'),
            ];
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name')->add('price')->add('active');
        }

        public function columns(): array
        {
            return [
                Column::make('Name', 'name')->searchable(),
                Column::make('Price', 'price'),
                Column::make('Active', 'active'),
            ];
        }
    };
}

function filterBuilderHiddenComponent(): PowerGridComponent
{
    return new class() extends PowerGridComponent
    {
        public string $tableName = 'fb-hidden';

        public function setUp(): array
        {
            return [PowerGrid::filterBuilder()->hideDefaultFilters()];
        }

        public function datasource()
        {
            return collect([['id' => 1, 'name' => 'Pastel']]);
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
}

function fbRow(string $column, string $operator, mixed $value = null, mixed $value2 = null, string $boolean = 'and'): array
{
    return compact('column', 'operator', 'value', 'value2', 'boolean');
}

it('combines conditions with AND', function () {
    Livewire::test(filterBuilderCollectionComponent()::class)
        ->set('filterBuilder', [
            'match' => 'and',
            'rows' => [
                fbRow('name', 'contains', 'a'),      // Pastel, Carne, Torta
                fbRow('price', 'greater_equal', '30'), // Carne, Sushi, Torta
            ],
        ])
        ->assertSee('Carne')
        ->assertSee('Torta')
        ->assertDontSee('Pastel')
        ->assertDontSee('Sushi');
});

it('combines conditions with a per-row OR connector', function () {
    Livewire::test(filterBuilderCollectionComponent()::class)
        ->set('filterBuilder', [
            'match' => 'and',
            'rows' => [
                fbRow('name', 'is', 'Pastel'),
                fbRow('name', 'is', 'Sushi', null, 'or'),
            ],
        ])
        ->assertSee('Pastel')
        ->assertSee('Sushi')
        ->assertDontSee('Carne')
        ->assertDontSee('Torta');
});

it('resolves mixed per-row AND/OR with SQL precedence', function () {
    // price >= 60  OR  (name is Pastel AND active is true)  ==  {Sushi, Pastel}
    Livewire::test(filterBuilderCollectionComponent()::class)
        ->set('filterBuilder', [
            'match' => 'and',
            'rows' => [
                fbRow('price', 'greater_equal', '60'),
                fbRow('name', 'is', 'Pastel', null, 'or'),
                fbRow('active', 'is', 'true', null, 'and'),
            ],
        ])
        ->assertSee('Pastel')
        ->assertSee('Sushi')
        ->assertDontSee('Carne')
        ->assertDontSee('Torta');
});

it('applies number between', function () {
    Livewire::test(filterBuilderCollectionComponent()::class)
        ->set('filterBuilder', [
            'match' => 'and',
            'rows' => [fbRow('price', 'between', '20', '55')],
        ])
        ->assertSee('Carne')
        ->assertSee('Torta')
        ->assertDontSee('Pastel')
        ->assertDontSee('Sushi');
});

it('applies input text starts_with', function () {
    Livewire::test(filterBuilderCollectionComponent()::class)
        ->set('filterBuilder', [
            'match' => 'and',
            'rows' => [fbRow('name', 'starts_with', 'S')],
        ])
        ->assertSee('Sushi')
        ->assertDontSee('Pastel')
        ->assertDontSee('Carne');
});

it('applies boolean is', function () {
    Livewire::test(filterBuilderCollectionComponent()::class)
        ->set('filterBuilder', [
            'match' => 'and',
            'rows' => [fbRow('active', 'is', 'true')],
        ])
        ->assertSee('Pastel')
        ->assertSee('Carne')
        ->assertDontSee('Sushi')
        ->assertDontSee('Torta');
});

it('does nothing when there are no conditions', function () {
    Livewire::test(filterBuilderCollectionComponent()::class)
        ->set('filterBuilder', ['match' => 'and', 'rows' => []])
        ->assertSee('Pastel')
        ->assertSee('Carne')
        ->assertSee('Sushi')
        ->assertSee('Torta');
});

it('applyFilterBuilder normalizes state and adds a pill', function () {
    Livewire::test(filterBuilderCollectionComponent()::class)
        ->call('applyFilterBuilder', [
            'match' => 'and',
            'rows' => [fbRow('name', 'contains', 'a')],
        ])
        ->assertSet('filterBuilder.match', 'and')
        ->assertSet('filterBuilder.rows.0.column', 'name')
        ->assertSet('filterBuilder.rows.0.operator', 'contains')
        ->assertSet('filterBuilder.rows.0.boolean', 'and')
        ->assertSet('enabledFilters.0.source', 'filterBuilder')
        ->assertSee('Carne')
        ->assertDontSee('Sushi');
});

it('clears a single builder row via its pill index', function () {
    Livewire::test(filterBuilderCollectionComponent()::class)
        ->call('applyFilterBuilder', [
            'match' => 'and',
            'rows' => [
                fbRow('name', 'contains', 'a'),
                fbRow('price', 'greater_equal', '30'),
            ],
        ])
        ->assertCount('filterBuilder.rows', 2)
        ->call('clearFilterBuilderRow', 1)
        ->assertCount('filterBuilder.rows', 1)
        ->assertSet('filterBuilder.rows.0.column', 'name')
        ->assertSee('Pastel')   // price condition removed, only "contains a" remains
        ->assertSee('Torta');
});

it('resetFilterBuilder empties the applied state', function () {
    Livewire::test(filterBuilderCollectionComponent()::class)
        ->call('applyFilterBuilder', ['match' => 'and', 'rows' => [fbRow('name', 'is', 'Pastel')]])
        ->assertCount('filterBuilder.rows', 1)
        ->call('resetFilterBuilder')
        ->assertCount('filterBuilder.rows', 0)
        ->assertSee('Pastel')
        ->assertSee('Sushi');
});

it('clearAllFilters also clears the builder', function () {
    Livewire::test(filterBuilderCollectionComponent()::class)
        ->call('applyFilterBuilder', ['match' => 'and', 'rows' => [fbRow('name', 'is', 'Pastel')]])
        ->assertCount('filterBuilder.rows', 1)
        ->call('clearAllFilters')
        ->assertCount('filterBuilder.rows', 0)
        ->assertSee('Sushi');
});

it('coexists with the global search (AND across features)', function () {
    Livewire::test(filterBuilderCollectionComponent()::class)
        ->set('search', 'Carne')
        ->set('filterBuilder', ['match' => 'and', 'rows' => [
            fbRow('name', 'is', 'Pastel'),
            fbRow('name', 'is', 'Carne', null, 'or'),
        ]])
        ->assertSee('Carne')
        ->assertDontSee('Pastel')  // filtered out by the search even though the OR group allows it
        ->assertDontSee('Sushi');
});

it('exposes column metadata for the modal', function () {
    $component = filterBuilderCollectionComponent();

    $meta = collect($component->filterBuilderMeta());

    expect($meta->pluck('field')->all())->toBe(['name', 'price', 'active'])
        ->and($meta->firstWhere('field', 'name')['operators'])->toContain('contains')
        ->and($meta->firstWhere('field', 'price')['operators'])->toBe(['between', 'greater_equal', 'less_equal']);
});

it('exposes yes/no options for boolean columns (not is/is not)', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'fb-boolean-options';

        public function setUp(): array
        {
            return [PowerGrid::filterBuilder()];
        }

        public function datasource()
        {
            return collect([['id' => 1, 'active' => true]]);
        }

        public function filters(): array
        {
            return [
                Filter::boolean('active')->label('Enabled', 'Disabled'),
            ];
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('active');
        }

        public function columns(): array
        {
            return [Column::make('Active', 'active')];
        }
    };

    $options = collect($component->filterBuilderMeta())->firstWhere('field', 'active')['options'];

    expect($options)->toBe([
        ['value' => 'true', 'label' => 'Enabled'],
        ['value' => 'false', 'label' => 'Disabled'],
    ]);
});

it('is disabled without the setUp opt-in', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'fb-disabled';

        public function datasource()
        {
            return collect([['id' => 1, 'name' => 'Pastel']]);
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

    $plugin = new FilterBuilderPlugin($component);

    expect($plugin->isEnabled())->toBeFalse()
        ->and($plugin->handlesZone('header'))->toBeFalse();
});

it('reports the hideDefaultFilters config through the component', function () {
    $default = filterBuilderCollectionComponent();
    $default->setUp = ['filterBuilder' => PowerGrid::filterBuilder()];

    $hidden = filterBuilderCollectionComponent();
    $hidden->setUp = ['filterBuilder' => PowerGrid::filterBuilder()->hideDefaultFilters()];

    expect($default->filterBuilderHidesDefaultFilters())->toBeFalse()
        ->and($hidden->filterBuilderHidesDefaultFilters())->toBeTrue();
});

it('renders the inline filters by default', function () {
    Livewire::test(filterBuilderCollectionComponent()::class)
        ->assertSee('input_text_fb-collection_name', false);
});

it('hides the inline filters when hideDefaultFilters is set', function () {
    Livewire::test(filterBuilderHiddenComponent()::class)
        ->assertDontSee('input_text_fb-hidden_name', false);
});

it('renders the header zone only under the Flux theme', function () {
    $component = filterBuilderCollectionComponent();
    $component->columns = $component->columns();
    $component->setUp = ['filterBuilder' => PowerGrid::filterBuilder()];

    $plugin = new FilterBuilderPlugin($component);

    app()->instance('powergrid.theme', new Tailwind());

    expect($plugin->isEnabled())->toBeTrue()
        ->and($plugin->handlesZone('header'))->toBeFalse();

    app()->instance('powergrid.theme', new Flux());

    expect($plugin->handlesZone('header'))->toBeTrue();
});

/* ---- Database datasource ---- */

it('applies builder conditions on an Eloquent query', function () {
    TestDatabase::up();

    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'fb-db';

        public function setUp(): array
        {
            return [PowerGrid::filterBuilder()];
        }

        public function datasource()
        {
            return Dish::query();
        }

        public function filters(): array
        {
            return [
                Filter::inputText('name'),
                Filter::number('price'),
            ];
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name')->add('price');
        }

        public function columns(): array
        {
            return [
                Column::make('Name', 'name'),
                Column::make('Price', 'price'),
            ];
        }
    };

    Livewire::test($component::class)
        ->set('filterBuilder', ['match' => 'and', 'rows' => [
            fbRow('name', 'starts_with', 'Barco'),
        ]])
        ->assertSee('Barco-Sushi da Sueli')
        ->assertSee('Barco-Sushi Simples')
        ->assertDontSee('Pastel de Nata');
})->group('database');

function filterBuilderPersistComponent(): PowerGridComponent
{
    return new class() extends PowerGridComponent
    {
        public string $tableName = 'fb-persist';

        public function setUp(): array
        {
            return [PowerGrid::filterBuilder()->persist()];
        }

        public function datasource()
        {
            return collect([
                ['id' => 1, 'name' => 'Pastel'],
                ['id' => 2, 'name' => 'Carne'],
            ]);
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
}

it('persists the builder via FilterBuilder::persist() without persist([...])', function () {
    config()->set('livewire-powergrid.persist_driver', 'session');

    Livewire::test(filterBuilderPersistComponent()::class)
        ->call('applyFilterBuilder', ['match' => 'and', 'rows' => [fbRow('name', 'contains', 'a')]]);

    expect(session('pg:fb-persist'))->toContain('filterBuilder');

    Livewire::test(filterBuilderPersistComponent()::class)
        ->assertSet('filterBuilder.rows.0.column', 'name')
        ->assertSet('filterBuilder.rows.0.operator', 'contains')
        ->assertSet('enabledFilters.0.source', 'filterBuilder');
})->group('filters');

it('does not persist the builder without an opt-in', function () {
    config()->set('livewire-powergrid.persist_driver', 'session');

    Livewire::test(filterBuilderCollectionComponent()::class)
        ->call('applyFilterBuilder', ['match' => 'and', 'rows' => [fbRow('name', 'contains', 'a')]]);

    expect(session('pg:fb-collection'))->toBeNull();
})->group('filters');

it('invokes beforeFilterBuilderApply with the validated conditions and lets it modify the query', function () {
    $component = new class() extends PowerGridComponent
    {
        /** @var array<string, mixed> */
        public array $trackedConditions = [];

        public string $tableName = 'fb-before-hook';

        public function setUp(): array
        {
            return [PowerGrid::filterBuilder()];
        }

        public function datasource()
        {
            return collect([
                ['id' => 1, 'name' => 'PastelActive', 'active' => true],
                ['id' => 2, 'name' => 'PastelInactive', 'active' => false],
            ]);
        }

        public function beforeFilterBuilderApply(mixed $query, array $conditions): mixed
        {
            $this->trackedConditions = $conditions;

            return $query->where('active', true);
        }

        public function filters(): array
        {
            return [Filter::inputText('name')];
        }

        public function fields(): PowerGridFields
        {
            return PowerGrid::fields()->add('id')->add('name')->add('active');
        }

        public function columns(): array
        {
            return [Column::make('Name', 'name'), Column::make('Active', 'active')];
        }
    };

    Livewire::test($component::class)
        ->call('applyFilterBuilder', ['match' => 'and', 'rows' => [fbRow('name', 'contains', 'Pastel')]])
        ->assertSet('trackedConditions.rows.0.column', 'name')
        ->assertSet('trackedConditions.rows.0.operator', 'contains')
        ->assertSee('PastelActive')        // matched the builder + the hook's active=true
        ->assertDontSee('PastelInactive'); // dropped by the hook's extra constraint
})->group('filters');

it('rejects the apply when validateFilterBuilder throws, and commits when it passes', function () {
    $component = new class() extends PowerGridComponent
    {
        public string $tableName = 'fb-validate-hook';

        public function setUp(): array
        {
            return [PowerGrid::filterBuilder()];
        }

        public function datasource()
        {
            return collect([['id' => 1, 'name' => 'Pastel']]);
        }

        public function validateFilterBuilder(array $conditions): void
        {
            foreach ($conditions['rows'] as $row) {
                if (($row['value'] ?? '') === 'bad') {
                    throw new InvalidArgumentException('Invalid condition');
                }
            }
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

    // Throws before committing → applied state is never touched.
    expect(fn () => Livewire::test($component::class)
        ->call('applyFilterBuilder', ['match' => 'and', 'rows' => [fbRow('name', 'contains', 'bad')]]))
        ->toThrow(InvalidArgumentException::class);

    Livewire::test($component::class)
        ->call('applyFilterBuilder', ['match' => 'and', 'rows' => [fbRow('name', 'contains', 'Pas')]])
        ->assertSet('filterBuilder.rows.0.value', 'Pas');
})->group('filters');
