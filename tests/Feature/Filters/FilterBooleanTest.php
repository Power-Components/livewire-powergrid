<?php

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Tests\{Concerns\Components\DishesIterableTable,
    Concerns\Components\DishesTable,
    Concerns\Components\DishesTableWithJoin};
use PowerComponents\LivewirePowerGrid\Themes\Tailwind;

use function PowerComponents\LivewirePowerGrid\Tests\Plugins\livewire;

it('properly filters by bool true', function (string $component, object $params) {
    $component = livewire($component, [
        'testFilters' => [
            Filter::boolean('in_stock')->label('yes', 'no'),
        ],
    ])
        ->call('setTestThemeClass', $params->theme)
        ->assertSee('In Stock')
        ->assertSeeHtml('wire:input.live.debounce.600ms="filterBoolean(\'in_stock\', $event.target.value, \'In Stock\')"');

    expect($component->filters)->toBeEmpty();

    $component->set('filters', filterBoolean('in_stock', 'true'))
        ->assertSee('Pastel de Nata')
        ->assertDontSee('Barco-Sushi da Sueli');

    expect($component->filters)->toMatchArray([
        'boolean' => [
            'in_stock' => 'true',
        ],
    ]);

    $component->call('clearFilter', 'in_stock');

    expect($component->filters)->toMatchArray([]);

    $component->assertSee('Barco-Sushi da Sueli');
})->with('filter_boolean_themes');

$customBuilder = new class() extends DishesTable
{
    public int $dishId;

    public function filters(): array
    {
        return [
            Filter::boolean('in_stock')
                ->builder(function ($builder, $values) {
                    expect($values)
                        ->toBe('true')
                        ->and($builder)->toBeInstanceOf(Builder::class);

                    return $builder->where('dishes.id', 1);
                })
                ->label('yes', 'no'),
        ];
    }
};

it('properly filters by bool true with custom builder', function (string $component, object $params) {
    $component = livewire($component)
        ->call('setTestThemeClass', $params->theme)
        ->assertSee('In Stock')
        ->assertSeeHtml('wire:input.live.debounce.600ms="filterBoolean(\'in_stock\', $event.target.value, \'In Stock\')"');

    expect($component->filters)->toBeEmpty();

    $component->set('filters', filterBoolean('in_stock', 'true'))
        ->assertSee('Pastel de Nata')
        ->assertDontSee('Peixada da chef Nábia');
})->group('filters', 'filterBoolean')
    ->with([
        'tailwind' => [$customBuilder::class, (object) ['theme' => Tailwind::class]],
    ]);

it('properly filters by bool true using collection table', function (string $component, string $theme) {
    $component = livewire($component, [
        'testFilters' => [
            Filter::boolean('in_stock')->label('yes', 'no'),
        ],
    ])
        ->call('setTestThemeClass', $theme)
        ->assertSee('In Stock')
        ->assertSeeHtml('wire:input.live.debounce.600ms="filterBoolean(\'in_stock\', $event.target.value, \'In Stock\')"');

    expect($component->filters)->toBeEmpty();

    $component->set('filters', filterBoolean('in_stock', 'true'))
        ->assertSee('Name 1')
        ->assertDontSee('Name 3');

    expect($component->filters)->toMatchArray([
        'boolean' => [
            'in_stock' => 'true',
        ],
    ]);

    $component->call('clearFilter', 'in_stock');

    $component->assertSee('Name 3');

    expect($component->filters)->toMatchArray([]);
})->group('filters', 'filterBoolean')
    ->with('filter_boolean_themes_iterable');

it('properly filters by bool true using collection with custom builder', function (string $componentName, string $theme) {
    $component = livewire($componentName, [
        'testFilters' => [
            Filter::boolean('in_stock', 'in_stock')
                ->label('yes', 'no'),
        ],
    ])
        ->call('setTestThemeClass', $theme)
        ->assertSeeHtml('wire:input.live.debounce.600ms="filterBoolean(\'in_stock\', $event.target.value, \'In Stock\')"')
        ->assertSeeHtml('wire:model="filters.boolean.in_stock"');

    expect($component->filters)->toBeEmpty();

    $component->set('filters', filterBoolean('in_stock', true))
        ->assertSee('Name 1')
        ->assertDontSee('Name 3');
})->group('filters', 'filterBoolean')
    ->with('filter_boolean_themes_iterable');

$customCollection = new class() extends DishesIterableTable
{
    public int $dishId;

    public function filters(): array
    {
        return [
            Filter::boolean('in_stock', 'in_stock')
                ->label('yes', 'no')
                ->collection(function ($collection, $values) {
                    expect($values)->toBe('true')
                        ->and($collection)->toBeInstanceOf(Collection::class);

                    return $collection->where('id', 1);
                }),
        ];
    }
};

it('properly filters by bool true using collection with custom builder using tablename in field', function (string $component, string $theme) {
    $component = livewire($component, ['iterableType' => 'collection'])
        ->call('setTestThemeClass', $theme)
        ->assertSeeHtml('wire:input.live.debounce.600ms="filterBoolean(\'in_stock\', $event.target.value, \'In Stock\')"');

    expect($component->filters)->toBeEmpty();

    $component->set('filters', [
        'boolean' => [
            'in_stock' => 'true',
        ],
    ])
        ->assertSee('Name 1')
        ->assertDontSee('Name 2');

    expect($component->filters)->toMatchArray([
        'boolean' => [
            'in_stock' => 'true',
        ],
    ]);
})->group('filters', 'filterBoolean')
    ->with([
        'tailwind' => [$customCollection::class, Tailwind::class],
    ]);

it('properly filters by bool false', function (string $component, object $params) {
    $component = livewire($component, [
        'testFilters' => [Filter::boolean('in_stock')->label('yes', 'no')],
    ])
        ->call('setTestThemeClass', $params->theme);

    expect($component->filters)->toMatchArray([]);

    $component->set('filters', filterBoolean('in_stock', 'false'))
        ->assertSee('Barco-Sushi da Sueli')
        ->assertDontSee('Pastel de Nata');

    expect($component->filters)->toMatchArray([
        'boolean' => [
            'in_stock' => 'false',
        ],
    ]);

    $component->call('clearFilter', 'in_stock')
        ->assertSee('Pastel de Nata');

    expect($component->filters)->toMatchArray([]);
})->group('filters', 'filterBoolean')
    ->with('filter_boolean_themes');

it('properly filters by bool false using collection', function (string $component, string $theme) {
    $component = livewire($component, [
        'testFilters' => [Filter::boolean('in_stock')->label('yes', 'no')],
    ])
        ->call('setTestThemeClass', $theme)
        ->assertSee('In Stock')
        ->assertSeeHtml('wire:input.live.debounce.600ms="filterBoolean(\'in_stock\', $event.target.value, \'In Stock\')"');

    expect($component->filters)->toMatchArray([]);

    $component->set('filters', filterBoolean('in_stock', 'false'))
        ->assertSee('Name 3')
        ->assertDontSee('Name 1');

    expect($component->filters)->toMatchArray([
        'boolean' => [
            'in_stock' => 'false',
        ],
    ]);

    $component->call('clearFilter', 'in_stock')
        ->assertSee('Name 1');

    expect($component->filters)->toMatchArray([]);
})->group('filters', 'filterBoolean')
    ->with('filter_boolean_themes_iterable');

it('properly filters by bool "all"', function (string $component, object $params) {
    $component = livewire($component, [
        'testFilters' => [Filter::boolean('in_stock')->label('yes', 'no')],
    ])
        ->call('setTestThemeClass', $params->theme);

    expect($component->filters)->toMatchArray([]);

    $component->set('filters', filterBoolean('in_stock', 'all'))
        ->assertSee('Pastel de Nata')
        ->assertSee('Barco-Sushi da Sueli');

    expect($component->filters)->toMatchArray([
        'boolean' => [
            'in_stock' => 'all',
        ],
    ]);
})->group('filters', 'filterBoolean')
    ->with('filter_boolean_themes');

it('properly filters by bool "all" using collection table', function (string $component, string $theme) {
    $component = livewire($component)
        ->call('setTestThemeClass', $theme);

    expect($component->filters)->toMatchArray([]);

    $component->set('filters', filterBoolean('in_stock', 'all'))
        ->assertSee('Name 1')
        ->assertSee('Name 3');

    expect($component->filters)->toMatchArray([
        'boolean' => [
            'in_stock' => 'all',
        ],
    ]);
})->group('filters', 'filterBoolean')
    ->with('filter_boolean_themes_iterable');

it('properly filters by bool true using action', function (string $component, object $params) {
    $component = livewire($component, [
        'testFilters' => [
            Filter::boolean('in_stock')->label('yes', 'no'),
        ],
    ])
        ->call('setTestThemeClass', $params->theme)
        ->assertSee('In Stock');

    $component->set('filters.boolean.in_stock', 'true')
        ->call('filterBoolean', 'in_stock', 'true', 'In Stock')
        ->assertSee('Pastel de Nata')
        ->assertDontSee('Barco-Sushi da Sueli');

    expect($component->filters)->toMatchArray([
        'boolean' => [
            'in_stock' => 'true',
        ],
    ]);
})->group('filters', 'filterBoolean')
    ->with('filter_boolean_themes');

it('properly filters by bool "all" using action', function (string $component, object $params) {
    $component = livewire($component, [
        'testFilters' => [
            Filter::boolean('in_stock')->label('yes', 'no'),
        ],
    ])
        ->call('setTestThemeClass', $params->theme);

    $component->set('filters.boolean.in_stock', 'true')
        ->call('filterBoolean', 'in_stock', 'true', 'In Stock');

    $component->set('filters.boolean.in_stock', 'all')
        ->call('filterBoolean', 'in_stock', 'all', 'In Stock')
        ->assertSee('Pastel de Nata')
        ->assertSee('Barco-Sushi da Sueli');

    expect($component->filters)->toMatchArray([]);
})->group('filters', 'filterBoolean')
    ->with('filter_boolean_themes');

dataset('filter_boolean_themes', [
    'tailwind' => [DishesTable::class, (object) ['theme' => Tailwind::class]],
    'tailwind with join' => [DishesTableWithJoin::class, (object) ['theme' => Tailwind::class]],
]);

dataset('filter_boolean_themes_iterable', [
    'tailwind' => [DishesIterableTable::class, Tailwind::class],
]);
