<?php

use PowerComponents\LivewirePowerGrid\Facades\Filter;

use function PowerComponents\LivewirePowerGrid\Tests\Plugins\livewire;

use PowerComponents\LivewirePowerGrid\Tests\{Concerns\Components\DishesArrayTable,
    Concerns\Components\DishesCollectionTable,
    Concerns\Components\DishesQueryBuilderTable,
    Concerns\Components\DishesTable,
    Concerns\Components\DishesTableWithJoin};

it('properly filters by bool true', function (string $component, object $params) {
    $component = livewire($component, [
        'testFilters' => [
            Filter::boolean('in_stock')->label('sim', 'não'),
        ],
    ])
        ->call($params->theme)
        ->assertSee('Em Estoque')
        ->assertSeeHtml('wire:input.live.debounce.600ms="filterBoolean(\'in_stock\', $event.target.value, \'Em Estoque\')"');

    expect($component->filters)
        ->toBeEmpty();

    $component->set('filters', filterBoolean('in_stock', 'true'))
        ->assertSee('Pastel de Nata')
        ->assertSee('Peixada da chef Nábia')
        ->assertSee('Carne Louca')
        ->assertSee('Bife à Rolê')
        ->assertSee('Francesinha vegana')
        ->assertDontSeeHtml('Francesinha </div>')
        ->assertDontSee('Barco-Sushi da Sueli')
        ->assertDontSee('Barco-Sushi Simples')
        ->assertDontSee('Polpetone Filé Mignon')
        ->assertDontSee('борщ');

    expect($component->filters)
        ->toMatchArray([
            'boolean' => [
                'in_stock' => 'true',
            ],
        ]);

    $component->call('clearFilter', 'in_stock');

    expect($component->filters)
        ->toMatchArray([]);

    $component->assertSee('Francesinha')
        ->assertSee('Barco-Sushi da Sueli')
        ->assertSee('Barco-Sushi Simples')
        ->assertSee('Polpetone Filé Mignon')
        ->assertSee('борщ');
})->group('filters', 'filterBoolean')
    ->with('filter_boolean_join', 'filter_boolean_query_builder');

$customBuilder = new class () extends DishesTable {
    public int $dishId;

    public function filters(): array
    {
        return [
            Filter::boolean('in_stock')
                ->builder(function ($builder, $values) {
                    expect($values)
                        ->toBe('true')
                        ->and($builder)->toBeInstanceOf(\Illuminate\Database\Eloquent\Builder::class);

                    return $builder->where('dishes.id', 1);
                })
                ->label('sim', 'não'),
        ];
    }
};

it('properly filters by bool true - custom builder', function (string $component, object $params) {
    $component = livewire($component)
        ->call($params->theme)
        ->assertSee('Em Estoque')
        ->assertSeeHtml('wire:input.live.debounce.600ms="filterBoolean(\'in_stock\', $event.target.value, \'Em Estoque\')"');

    expect($component->filters)
        ->toBeEmpty();

    $component->set('filters', filterBoolean('in_stock', 'true'))
        ->assertSee('Pastel de Nata')
        ->assertDontSee('Peixada da chef Nábia');
})->group('filters', 'filterBoolean')
    ->with([
        'tailwind -> id'  => [$customBuilder::class, (object) ['theme' => 'tailwind']],
        'bootstrap -> id' => [$customBuilder::class, (object) ['theme' => 'bootstrap']],
    ]);

it('properly filters by bool true - using collection & array table', function (string $component, string $theme) {
    $component = livewire($component, [
        'testFilters' => [
            Filter::boolean('in_stock')->label('sim', 'não'),
        ],
    ])
        ->call($theme)
        ->assertSee('In Stock')
        ->assertSeeHtml('wire:input.live.debounce.600ms="filterBoolean(\'in_stock\', $event.target.value, \'In Stock\')"');

    expect($component->filters)
        ->toBeEmpty();

    $component ->set('filters', filterBoolean('in_stock', 'true'))
        ->assertSee('Name 1')
        ->assertSee('Name 2')
        ->assertSee('Name 4')
        ->assertDontSee('Name 3')
        ->assertDontSee('Name 5');

    expect($component->filters)
        ->toMatchArray([
            'boolean' => [
                'in_stock' => 'true',
            ],
        ]);

    $component->call('clearFilter', 'in_stock');

    $component->assertSee('Name 1')
        ->assertSee('Name 2')
        ->assertSee('Name 3')
        ->assertSee('Name 4')
        ->assertSee('Name 5');

    expect($component->filters)
        ->toMatchArray([]);
})->group('filters', 'filterBoolean')
    ->with('filter_boolean_themes_collection', 'filter_boolean_themes_array');

it('properly filters by bool true - using collection', function (string $component, string $theme) {
    $component = livewire($component, [
        'testFilters' => [Filter::boolean('in_stock')->label('sim', 'não')],
    ])
        ->call($theme)
        ->assertSee('In Stock')
        ->assertSeeHtml('wire:input.live.debounce.600ms="filterBoolean(\'in_stock\', $event.target.value, \'In Stock\')"');

    expect($component->filters)
        ->toBeEmpty();

    $component ->set('filters', filterBoolean('in_stock', 'true'))
        ->assertSee('Name 1')
        ->assertSee('Name 2')
        ->assertSee('Name 4')
        ->assertDontSee('Name 3')
        ->assertDontSee('Name 5');

    expect($component->filters)
        ->toMatchArray([
            'boolean' => [
                'in_stock' => 'true',
            ],
        ]);

    $component->call('clearFilter', 'in_stock');

    $component->assertSee('Name 1')
        ->assertSee('Name 2')
        ->assertSee('Name 3')
        ->assertSee('Name 4')
        ->assertSee('Name 5');

    expect($component->filters)
        ->toMatchArray([]);
})->group('filters', 'filterBoolean')
    ->with('filter_boolean_themes_collection');

it('properly filters by bool true - using collection - custom builder', function (string $componentName, string $theme) {
    $component = livewire($componentName, [
        'testFilters' => [
            Filter::boolean('in_stock', 'in_stock')
                ->label('yes', 'no'),
        ],
    ])
        ->call($theme)
        ->assertSeeHtml('wire:input.live.debounce.600ms="filterBoolean(\'in_stock\', $event.target.value, \'In Stock\')"')
        ->assertSeeHtml('wire:model="filters.boolean.in_stock"');

    expect($component->filters)
        ->toBeEmpty();

    $component ->set('filters', filterBoolean('in_stock', true))
        ->assertSee('Name 1')
        ->assertSee('Name 2')
        ->assertDontSee('Name 3')
        ->assertSee('Name 4')
        ->assertDontSee('Name 5');
})->group('filters', 'filterBoolean')
    ->with('filter_boolean_themes_collection');

$customCollection = new class () extends DishesCollectionTable {
    public int $dishId;

    public function filters(): array
    {
        return [
            Filter::boolean('in_stock', 'in_stock')
                ->label('yes', 'no')
                ->collection(function ($collection, $values) {
                    expect($values)->toBe('true')
                        ->and($collection)->toBeInstanceOf(\Illuminate\Support\Collection::class);

                    return $collection->where('id', 1);
                }),
        ];
    }
};

it('properly filters by bool true - using collection - custom builder - using tablename in field', function (string $component, string $theme) {
    $component = livewire($component)
        ->call($theme)
        ->assertSeeHtml('wire:input.live.debounce.600ms="filterBoolean(\'in_stock\', $event.target.value, \'In Stock\')"');

    expect($component->filters)
        ->toBeEmpty();

    $component->set('filters', [
        'boolean' => [
            'in_stock' => 'true',
        ],
    ])
        ->assertSee('Name 1')
        ->assertDontSee('Name 2')
        ->assertDontSee('Name 3')
        ->assertDontSee('Name 4')
        ->assertDontSee('Name 5');

    expect($component->filters)
        ->toMatchArray([
            'boolean' => [
                'in_stock' => 'true',
            ],
        ]);
})->group('filters', 'filterBoolean')
    ->with([
        'tailwind -> id'  => [$customCollection::class, 'tailwind'],
        'bootstrap -> id' => [$customCollection::class, 'bootstrap'],
    ]);

it('properly filters by bool false', function (string $component, object $params) {
    $component = livewire($component, [
        'testFilters' => [Filter::boolean('in_stock')->label('sim', 'não')],
    ])
        ->call($params->theme);

    expect($component->filters)
        ->toMatchArray([]);

    $component->set('filters', filterBoolean('in_stock', 'false'))
        ->assertSee('Francesinha')
        ->assertSee('Barco-Sushi da Sueli')
        ->assertSee('Barco-Sushi Simples')
        ->assertSee('Polpetone Filé Mignon')
        ->assertSee('борщ')
        ->assertDontSee('Pastel de Nata')
        ->assertDontSee('Peixada da chef Nábia')
        ->assertDontSee('Carne Louca')
        ->assertDontSee('Bife à Rolê')
        ->assertDontSee('Francesinha vegana');

    expect($component->filters)
        ->toMatchArray([
            'boolean' => [
                'in_stock' => 'false',
            ],
        ]);

    $component->call('clearFilter', 'in_stock')
        ->assertSee('Pastel de Nata')
        ->assertSee('Peixada da chef Nábia')
        ->assertSee('Carne Louca')
        ->assertSee('Bife à Rolê')
        ->assertSee('Francesinha vegana');

    expect($component->filters)
        ->toMatchArray([]);
})->group('filters', 'filterBoolean')
    ->with('filter_boolean_join', 'filter_boolean_query_builder');

it('properly filters by bool false - using collection & array', function (string $component, string $theme) {
    $component = livewire($component, [
        'testFilters' => [Filter::boolean('in_stock')->label('sim', 'não')],
    ])
        ->call($theme)
        ->assertSee('In Stock')
        ->assertSeeHtml('wire:input.live.debounce.600ms="filterBoolean(\'in_stock\', $event.target.value, \'In Stock\')"');

    expect($component->filters)
        ->toMatchArray([]);

    $component->set('filters', filterBoolean('in_stock', 'false'))
        ->assertSee('Name 3')
        ->assertSee('Name 5')
        ->assertDontSee('Name 1')
        ->assertDontSee('Name 2')
        ->assertDontSee('Name 4');

    expect($component->filters)
        ->toMatchArray([
            'boolean' => [
                'in_stock' => 'false',
            ],
        ]);

    $component->call('clearFilter', 'in_stock')
        ->assertSee('Name 1')
        ->assertSee('Name 2')
        ->assertSee('Name 3')
        ->assertSee('Name 4')
        ->assertSee('Name 5');

    expect($component->filters)
        ->toMatchArray([]);
})->group('filters', 'filterBoolean')
    ->with('filter_boolean_themes_collection', 'filter_boolean_themes_array');

it('properly filters by bool "all"', function (string $component, object $params) {
    $component = livewire($component, [
        'testFilters' => [Filter::boolean('in_stock')->label('sim', 'não')],
    ])
        ->call($params->theme);

    expect($component->filters)
        ->toMatchArray([]);

    $component->set('filters', filterBoolean('in_stock', 'all'))
        ->assertSee('Pastel de Nata')
        ->assertSee('Peixada da chef Nábia')
        ->assertSee('Carne Louca')
        ->assertSee('Bife à Rolê')
        ->assertSee('Francesinha vegana')
        ->assertSee('Francesinha')
        ->assertSee('Barco-Sushi da Sueli')
        ->assertSee('Barco-Sushi Simples')
        ->assertSee('Polpetone Filé Mignon')
        ->assertSee('борщ');

    expect($component->filters)
        ->toMatchArray([
            'boolean' => [
                'in_stock' => 'all',
            ],
        ]);
})->group('filters', 'filterBoolean')
    ->with('filter_boolean_join', 'filter_boolean_query_builder');

it('properly filters by bool "all" - using collection & array table', function (string $component, string $theme) {
    $component = livewire($component)
        ->call($theme);

    expect($component->filters)
        ->toMatchArray([]);

    $component->set('filters', filterBoolean('in_stock', 'all'))
        ->assertSee('Name 1')
        ->assertSee('Name 2')
        ->assertSee('Name 3')
        ->assertSee('Name 4')
        ->assertSee('Name 5');

    expect($component->filters)
        ->toMatchArray([
            'boolean' => [
                'in_stock' => 'all',
            ],
        ]);
})->group('filters', 'filterBoolean')
    ->with('filter_boolean_themes_collection', 'filter_boolean_themes_array');

dataset('filter_boolean_join', [
    'tailwind -> id'         => [DishesTable::class, (object) ['theme' => 'tailwind', 'field' => 'id']],
    'bootstrap -> id'        => [DishesTable::class, (object) ['theme' => 'bootstrap', 'field' => 'id']],
    'tailwind -> dishes.id'  => [DishesTableWithJoin::class, (object) ['theme' => 'tailwind', 'field' => 'dishes.id']],
    'bootstrap -> dishes.id' => [DishesTableWithJoin::class, (object) ['theme' => 'bootstrap', 'field' => 'dishes.id']],
]);

dataset('filter_boolean_query_builder', [
    'tailwind query builder -> id'  => [DishesQueryBuilderTable::class, (object) ['theme' => 'tailwind', 'field' => 'id']],
    'bootstrap query builder -> id' => [DishesQueryBuilderTable::class, (object) ['theme' => 'bootstrap', 'field' => 'id']],
]);

dataset('filter_boolean_themes_array', [
    [DishesArrayTable::class, 'tailwind'],
    [DishesArrayTable::class, 'bootstrap'],
]);

dataset('filter_boolean_themes_collection', [
    'tailwind'  => [DishesCollectionTable::class, 'tailwind'],
    'bootstrap' => [DishesCollectionTable::class, 'bootstrap'],
]);
