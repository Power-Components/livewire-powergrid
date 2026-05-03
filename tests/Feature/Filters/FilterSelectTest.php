<?php

use Illuminate\Database\Eloquent\{Builder, Collection};
use Livewire\Attributes\Computed;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Tests\{
    Concerns\Components\DishesIterableTable,
    Concerns\Components\DishesQueryBuilderTable,
    Concerns\Components\DishesTable,
    Concerns\Components\DishesTableWithJoin
};
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Models\Category;
use PowerComponents\LivewirePowerGrid\Themes\Tailwind;

use function PowerComponents\LivewirePowerGrid\Tests\Plugins\livewire;

it('displays filter with results and options', function (string $component, object $params) {
    $select = Filter::select('category_name', 'category_id')
        ->dataSource(Category::all())
        ->optionValue('category_id')
        ->optionLabel('category_name');

    livewire($component, [
        'testFilters' => [$select],
    ])
        ->call('setTestThemeClass', $params->theme)
        ->assertSeeHtmlInOrder([
            'wire:model="filters.select.category_id"',
            'wire:input.live.debounce.600ms="filterSelect(\'category_id\', \'Category\')"',
            '<option value="">All</option>',
        ]);
})->with('filter_select_themes');

$customBuilder = new class() extends DishesTable
{
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

it('filters with custom builder', function (string $component, object $params) {
    livewire($component)
        ->call('setTestThemeClass', $params->theme)
        ->set('filters', filterSelect('category_id', 1))
        ->assertSee('Pastel de Nata')
        ->assertDontSee('Peixada da chef Nábia');
})->with([
    'tailwind' => [$customBuilder::class, (object) ['theme' => Tailwind::class]],
]);

$customCollection = new class() extends DishesIterableTable
{
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
                        ->and($builder)->toBeInstanceOf(Illuminate\Support\Collection::class);

                    return $builder->where('id', 2);
                }),
        ];
    }
};

it('filters with custom collection', function (string $component, object $params) {
    livewire($component, ['iterableType' => 'collection'])
        ->call('setTestThemeClass', $params->theme)
        ->set('filters', filterSelect('id', 2))
        ->assertSee('Name 2')
        ->assertDontSee('Name 1');
})->with([
    'tailwind' => [$customCollection::class, (object) ['theme' => Tailwind::class]],
]);

$computedDatasource = new class() extends DishesTable
{
    #[Computed]
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

it('filters with computed datasource', function (string $component, object $params) {
    livewire($component)
        ->call('setTestThemeClass', $params->theme)
        ->assertSee('Pastel de Nata')
        ->set('filters', filterSelect('category_id', 1))
        ->assertSee('Almôndegas ao Sugo')
        ->assertDontSee('Pastel de Nata');
})->with([
    'tailwind' => [$computedDatasource::class, (object) ['theme' => Tailwind::class]],
]);

it('filters by category id', function (string $component, object $params) {
    livewire($component)
        ->call('setTestThemeClass', $params->theme)
        ->set('testFilters', [
            Filter::inputText('category_id')->operators(),
        ])
        ->set('filters', filterSelect('category_id', 1))
        ->assertSee('Peixada da chef Nábia')
        ->assertDontSee('Pastel de Nata');
})->with('filter_select_themes');

it('filters by another category id', function (string $component, object $params) {
    livewire($component)
        ->call('setTestThemeClass', $params->theme)
        ->set('testFilters', [
            Filter::inputText('category_id')->operators(),
        ])
        ->set('filters', filterSelect('category_id', 3))
        ->assertDontSee('Peixada da chef Nábia')
        ->assertDontSee('Pastel de Nata');
})->with('filter_select_themes');

it('filters using same model field', function (string $component, object $params) {
    livewire($component)
        ->call('setTestThemeClass', $params->theme)
        ->set('testFilters', [
            Filter::inputText('serving_at')->operators(),
        ])
        ->set('filters', filterSelect('serving_at', 'table'))
        ->assertSee('Pastel de Nata')
        ->assertDontSee('Peixada da chef Nábia')
        ->set('filters', filterSelect('serving_at', 'pool bar'))
        ->assertSee('Peixada da chef Nábia')
        ->assertDontSee('Pastel de Nata')
        ->set('filters', filterSelect('serving_at', 'bar'))
        ->assertDontSee('Peixada da chef Nábia')
        ->assertDontSee('Pastel de Nata');
})->with('filter_select_themes');

dataset('filter_select_themes', [
    'tailwind' => [DishesTable::class, (object) ['theme' => Tailwind::class]],
    'tailwind with join' => [DishesTableWithJoin::class, (object) ['theme' => Tailwind::class]],
    'tailwind query builder' => [DishesQueryBuilderTable::class, (object) ['theme' => Tailwind::class]],
]);

function filterSelect(string $dataField, ?string $value): array
{
    return [
        'select' => [
            $dataField => $value,
        ],
    ];
}
