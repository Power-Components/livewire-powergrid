<?php

use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Tests\{Concerns\Components\DishesIterableTable,
    Concerns\Components\DishesTable,
    Concerns\Components\DishesTableWithJoin};

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
                        ->and($builder)->toBeInstanceOf(\Illuminate\Database\Eloquent\Builder::class);

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
        'tailwind' => [$customBuilder::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\Tailwind::class]],
        'bootstrap' => [$customBuilder::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\Bootstrap5::class]],
        'daisyui' => [$customBuilder::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\DaisyUI::class]],
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
                        ->and($collection)->toBeInstanceOf(\Illuminate\Support\Collection::class);

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
        'tailwind' => [$customCollection::class, \PowerComponents\LivewirePowerGrid\Themes\Tailwind::class],
        'bootstrap' => [$customCollection::class, \PowerComponents\LivewirePowerGrid\Themes\Bootstrap5::class],
        'daisyui' => [$customCollection::class, \PowerComponents\LivewirePowerGrid\Themes\DaisyUI::class],
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
    'tailwind' => [DishesTable::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\Tailwind::class]],
    'bootstrap' => [DishesTable::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\Bootstrap5::class]],
    'daisyui' => [DishesTable::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\DaisyUI::class]],
    'tailwind with join' => [DishesTableWithJoin::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\Tailwind::class]],
    'bootstrap with join' => [DishesTableWithJoin::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\Bootstrap5::class]],
    'daisyui with join' => [DishesTableWithJoin::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\DaisyUI::class]],
]);

dataset('filter_boolean_themes_iterable', [
    'tailwind' => [DishesIterableTable::class, \PowerComponents\LivewirePowerGrid\Themes\Tailwind::class],
    'bootstrap' => [DishesIterableTable::class, \PowerComponents\LivewirePowerGrid\Themes\Bootstrap5::class],
    'daisyui' => [DishesIterableTable::class, \PowerComponents\LivewirePowerGrid\Themes\DaisyUI::class],
]);

$defaultBooleanTrue = new class() extends DishesTable
{
    public function filters(): array
    {
        return [
            Filter::boolean('in_stock')
                ->label('yes', 'no')
                ->default('true'),
        ];
    }
};

$defaultBooleanFalse = new class() extends DishesTable
{
    public function filters(): array
    {
        return [
            Filter::boolean('in_stock')
                ->label('yes', 'no')
                ->default('false'),
        ];
    }
};

it('applies default value true to boolean filter on initial load', function (string $component, object $params) {
    $component = livewire($component)
        ->call('setTestThemeClass', $params->theme);

    expect($component->filters)->toMatchArray([
        'boolean' => [
            'in_stock' => 'true',
        ],
    ]);

    // Should show items that are in stock and not out of stock items
    $component->assertSee('Pastel de Nata')
        ->assertDontSee('Barco-Sushi da Sueli');
})->group('filters', 'filterBoolean')
    ->with([
        'tailwind' => [$defaultBooleanTrue::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\Tailwind::class]],
        'bootstrap' => [$defaultBooleanTrue::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\Bootstrap5::class]],
        'daisyui' => [$defaultBooleanTrue::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\DaisyUI::class]],
    ]);

it('applies default value false to boolean filter on initial load', function (string $component, object $params) {
    $component = livewire($component)
        ->call('setTestThemeClass', $params->theme);

    expect($component->filters)->toMatchArray([
        'boolean' => [
            'in_stock' => 'false',
        ],
    ]);

    // Should show items that are out of stock and not in stock items
    $component->assertSee('Barco-Sushi da Sueli')
        ->assertDontSee('Pastel de Nata');
})->group('filters', 'filterBoolean')
    ->with([
        'tailwind' => [$defaultBooleanFalse::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\Tailwind::class]],
        'bootstrap' => [$defaultBooleanFalse::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\Bootstrap5::class]],
        'daisyui' => [$defaultBooleanFalse::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\DaisyUI::class]],
    ]);

it('can clear default boolean filter', function (string $component, object $params) {
    $component = livewire($component)
        ->call('setTestThemeClass', $params->theme);

    expect($component->filters)->toMatchArray([
        'boolean' => [
            'in_stock' => 'true',
        ],
    ]);

    $component->call('clearFilter', 'in_stock');

    expect($component->filters)->toMatchArray([]);

    // Should now show all items including out of stock
    $component->assertSee('Barco-Sushi da Sueli');
})->group('filters', 'filterBoolean')
    ->with([
        'tailwind' => [$defaultBooleanTrue::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\Tailwind::class]],
        'bootstrap' => [$defaultBooleanTrue::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\Bootstrap5::class]],
        'daisyui' => [$defaultBooleanTrue::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\DaisyUI::class]],
    ]);

$defaultBooleanAll = new class() extends DishesTable
{
    public function filters(): array
    {
        return [
            Filter::boolean('in_stock')
                ->label('yes', 'no')
                ->default('all'),
        ];
    }
};

it('applies default value all to boolean filter on initial load', function (string $component, object $params) {
    $component = livewire($component)
        ->call('setTestThemeClass', $params->theme);

    expect($component->filters)->toMatchArray([
        'boolean' => [
            'in_stock' => 'all',
        ],
    ]);

    // Should show all items - both in stock and out of stock
    $component->assertSee('Pastel de Nata')
        ->assertSee('Barco-Sushi da Sueli');
})->group('filters', 'filterBoolean')
    ->with([
        'tailwind' => [$defaultBooleanAll::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\Tailwind::class]],
        'bootstrap' => [$defaultBooleanAll::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\Bootstrap5::class]],
        'daisyui' => [$defaultBooleanAll::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\DaisyUI::class]],
    ]);
