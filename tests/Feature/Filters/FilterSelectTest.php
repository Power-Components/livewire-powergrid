<?php

use Illuminate\Database\Eloquent\{Builder, Collection};
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Models\Category;

use function PowerComponents\LivewirePowerGrid\Tests\Plugins\livewire;

use PowerComponents\LivewirePowerGrid\Tests\{
    Concerns\Components\DishTableBase,
    Concerns\Components\DishesArrayTable,
    Concerns\Components\DishesCollectionTable,
    Concerns\Components\DishesQueryBuilderTable,
    Concerns\Components\DishesTable,
    Concerns\Components\DishesTableWithJoin
};

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
            'wire:model="filters.select.category_id"',
            'wire:input.live.debounce.600ms="filterSelect(\'category_id\', \'Category\')"',
            '<option value="">All</option>',
        ]);
})->group('filters', 'filterSelect')
    ->with('filter_select_join', 'filter_select_query_builder');

$customBuilder = new class () extends DishTableBase {
    public int $dishId;

    public function filters(): array
    {
        return [
            Filter::select('category_name', 'category_id')
                ->builder(function ($builder, $values) {
                    expect($values)
                        ->toBe('1')
                        ->and($builder)->toBeInstanceOf(Builder::class);

                    return $builder->where('dishes.id', 1);
                })
                ->dataSource(Category::all())
                ->optionValue('category_id')
                ->optionLabel('category_name'),
        ];
    }
};

$customCollection = new class () extends DishesCollectionTable {
    public int $dishId;

    public function filters(): array
    {
        return [
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
        ];
    }
};

$computedDatasource = new class () extends DishTableBase {
    public int $dishId;

    #[\Livewire\Attributes\Computed]
    public function getAllCategories(): Collection
    {
        return Category::all();
    }

    public function filters(): array
    {
        return [
            Filter::select('category_name', 'category_id')
                ->computedDatasource('getAllCategories')
                ->optionValue('id')
                ->optionLabel('name'),
        ];
    }
};

it('property filter using custom builder', function (string $component, object $params) {
    livewire($component)
        ->call($params->theme)
        ->set('filters', filterSelect('category_id', 1))
        ->assertSee('Pastel de Nata')
        ->assertDontSee('Peixada da chef Nábia');
    ;
})->group('filters', 'filterSelect')
    ->with([
        'tailwind -> id'  => [$customBuilder::class, (object) ['theme' => 'tailwind', 'field' => 'id']],
        'bootstrap -> id' => [$customBuilder::class, (object) ['theme' => 'bootstrap', 'field' => 'id']],
    ]);

it('property filter using computed datasource', function (string $component, object $params) {
    livewire($component)
        ->call($params->theme)
        ->assertSee('Pastel de Nata')
        ->set('filters', filterSelect('category_id', 1))
        ->assertSee('Almôndegas ao Sugo')
        ->assertDontSee('Pastel de Nata');
    ;
})->group('filters', 'filterSelect')
    ->with([
        'tailwind -> id'  => [$computedDatasource::class, (object) ['theme' => 'tailwind', 'field' => 'id']],
        'bootstrap -> id' => [$computedDatasource::class, (object) ['theme' => 'bootstrap', 'field' => 'id']],
    ]);

it('property filter using custom collection', function (string $component) {
    livewire($component)
        ->set('filters', filterSelect('id', 2))
        ->assertSee('Name 2')
        ->assertDontSee('Name 1')
        ->assertDontSee('Name 3');
})->group('filters', 'filterSelect')
    ->with([
        'tailwind -> id'  => [$customCollection::class, (object) ['theme' => 'tailwind', 'field' => 'id']],
        'bootstrap -> id' => [$customCollection::class, (object) ['theme' => 'bootstrap', 'field' => 'id']],
    ]);

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
    ->with('filter_select_join', 'filter_select_query_builder');

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
    ->with('filter_select_join', 'filter_select_query_builder');

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
})->group('filters', 'filterSelect')
    ->with('filter_select_join', 'filter_select_query_builder');

dataset('filter_select_join', [
    'tailwind -> id'         => [DishesTable::class, (object) ['theme' => 'tailwind', 'field' => 'id']],
    'bootstrap -> id'        => [DishesTable::class, (object) ['theme' => 'bootstrap', 'field' => 'id']],
    'tailwind -> dishes.id'  => [DishesTableWithJoin::class, (object) ['theme' => 'tailwind', 'field' => 'dishes.id']],
    'bootstrap -> dishes.id' => [DishesTableWithJoin::class, (object) ['theme' => 'bootstrap', 'field' => 'dishes.id']],
]);

dataset('filter_select_query_builder', [
    'tailwind query builder -> id'  => [DishesQueryBuilderTable::class, (object) ['theme' => 'tailwind', 'field' => 'id']],
    'bootstrap query builder -> id' => [DishesQueryBuilderTable::class, (object) ['theme' => 'bootstrap', 'field' => 'id']],
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
