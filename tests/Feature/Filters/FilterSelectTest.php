<?php

use function Pest\Livewire\livewire;

use PowerComponents\LivewirePowerGrid\Filters\Filter;

use PowerComponents\LivewirePowerGrid\Tests\Models\Category;
use PowerComponents\LivewirePowerGrid\Tests\{DishesArrayTable, DishesCollectionTable, DishesTable, DishesTableWithJoin};

it('property displays the results and options', function (string $component, object $params) {
    $select = Filter::select('category_name', 'category_id')
        ->dataSource(Category::all())
        ->optionValue('category_id')
        ->optionLabel('category_name');
    livewire($component, [
        'testFilters' => [$select],
    ])
        ->call($params->theme)
        ->assertSeeHtmlInOrder([
            'wire:model.debounce.500ms="filters.select.category_id"',
            'wire:input.debounce.500ms="filterSelect(\'category_id\', \'Category\')"',
            '<option value="">All</option>',
        ]);
})->group('filters', 'filterSelect')
    ->with('filter_select_join');

it('property filter using custom builder', function (string $component, object $params) {
    $component = livewire($component, [
        'testFilters' => [
            Filter::select('category_name', 'category_id')
                ->builder(function ($builder, $values) {
                    expect($values)
                        ->toBe('1')
                        ->and($builder)->toBeInstanceOf(\Illuminate\Database\Eloquent\Builder::class);

                    return $builder->where('dishes.id', 1);
                })
                ->dataSource(Category::all())
                ->optionValue('category_id')
                ->optionLabel('category_name'),
        ],
    ])
        ->call($params->theme)
        ->set('filters', filterSelect('category_id', 1))
        ->assertSee('Pastel de Nata')
        ->assertDontSee('Peixada da chef Nábia');

    $component->set('testFilters', [
        Filter::select('category_name', 'category_id')
            ->builder(function ($builder, $values) {
                expect($values)
                    ->toBe('1')
                    ->and($builder)->toBeInstanceOf(\Illuminate\Database\Eloquent\Builder::class);

                return $builder->where('dishes.id', 2);
            })
            ->dataSource(Category::all())
            ->optionValue('category_id')
            ->optionLabel('category_name'),
    ])
        ->set('filters', filterSelect('category_id', 1))
        ->assertDontSee('Pastel de Nata')
        ->assertSee('Peixada da chef Nábia');
})->group('filters', 'filterSelect')
    ->with('filter_select_join');

it('property filter using custom collection', function (string $component) {
    livewire($component, [
        'testFilters' => [
            Filter::select('id')
                ->dataSource(collect([['id' => 1, 'value' => 1], ['id' => 2, 'value' => 2]]))
                ->optionValue('id')
                ->optionLabel('value')
                ->collection(function ($builder, $values) {
                    expect($values)
                        ->toBe('2')
                        ->and($builder)->toBeInstanceOf(\Illuminate\Support\Collection::class);

                    return $builder->where('id', 2);
                }),
        ],
    ])
        ->set('filters', filterSelect('id', 2))
        ->assertSee('Name 2')
        ->assertDontSee('Name 1')
        ->assertDontSee('Name 3');
})->group('filters', 'filterSelect')
    ->with('filter_select_themes_collection', 'filter_select_themes_array');

it('property filter using collection & array', function (string $component) {
    livewire($component, [
        'testFilters' => [
            Filter::select('id')
                ->dataSource(collect([['id' => 1, 'value' => 1], ['id' => 2, 'value' => 2]]))
                ->optionValue('id')
                ->optionLabel('value'),
        ],
    ])
        ->set('filters', filterSelect('id', 2))
        ->assertSee('Name 2')
        ->assertDontSee('Name 1')
        ->assertDontSee('Name 3');
})->group('filters', 'filterSelect')
    ->with('filter_select_themes_collection', 'filter_select_themes_array');

it('properly filter with category_id', function (string $component, object $params) {
    livewire($component)
        ->call($params->theme)
        ->set('testFilters', [
            Filter::inputText('category_id')->operators(),
        ])
        ->set('filters', filterSelect('category_id', 1))
        ->assertSee('Peixada da chef Nábia')
        ->assertSee('Carne Louca')
        ->assertSee('Bife à Rolê')
        ->assertDontSee('Pastel de Nata');
})->group('filters', 'filterSelect')
    ->with('filter_select_join');

it('properly filter with another category_id', function (string $component, object $params) {
    livewire($component)
        ->call($params->theme)
        ->set('testFilters', [
            Filter::inputText('category_id')->operators(),
        ])
        ->set('filters', filterSelect('category_id', 3))
        ->assertDontSee('Peixada da chef Nábia')
        ->assertDontSee('Carne Louca')
        ->assertDontSee('Bife à Rolê');
})->group('filters', 'filterSelect')
    ->with('filter_select_join');

it('properly filters using the same model as the component', function (string $component, object $params) {
    livewire($component)
        ->call($params->theme)
        ->set('testFilters', [
            Filter::inputText('serving_at')->operators(),
        ])
        ->set('filters', filterSelect('serving_at', 'table'))
            ->assertSee('Pastel de Nata')
            ->assertDontSee('Peixada da chef Nábia')
            ->assertDontSee('Carne Louca')
            ->assertDontSee('Bife à Rolê')
            ->assertDontSee('Francesinha vegana')
        ->set('filters', filterSelect('serving_at', 'pool bar'))
            ->assertSee('Peixada da chef Nábia')
            ->assertSee('Carne Louca')
            ->assertSee('Bife à Rolê')
            ->assertSee('Francesinha vegana')
            ->assertDontSee('Pastel de Nata')
        ->set('filters', filterSelect('serving_at', 'bar'))
            ->assertDontSee('Peixada da chef Nábia')
            ->assertDontSee('Carne Louca')
            ->assertDontSee('Bife à Rolê')
            ->assertDontSee('Francesinha vegana')
            ->assertDontSee('Pastel de Nata');
})->group('filters', 'filterSelect')->with('filter_select_join');

dataset('filter_select_join', [
    'tailwind -> id'         => [DishesTable::class, (object) ['theme' => 'tailwind', 'field' => 'id']],
    'bootstrap -> id'        => [DishesTable::class, (object) ['theme' => 'bootstrap', 'field' => 'id']],
    'tailwind -> dishes.id'  => [DishesTableWithJoin::class, (object) ['theme' => 'tailwind', 'field' => 'dishes.id']],
    'bootstrap -> dishes.id' => [DishesTableWithJoin::class, (object) ['theme' => 'bootstrap', 'field' => 'dishes.id']],
]);

dataset('filter_select_themes_array', [
    [DishesArrayTable::class, 'tailwind'],
    [DishesArrayTable::class, 'bootstrap'],
]);

dataset('filter_select_themes_collection', [
    'tailwind'  => [DishesCollectionTable::class, 'tailwind'],
    'bootstrap' => [DishesCollectionTable::class, 'bootstrap'],
]);

function filterSelect(string $dataField, ?string $value): array
{
    return [
        'select' => [
            $dataField => $value,
        ],
    ];
}
