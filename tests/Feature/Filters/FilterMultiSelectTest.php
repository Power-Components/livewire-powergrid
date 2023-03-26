<?php

use function Pest\Livewire\livewire;

use PowerComponents\LivewirePowerGrid\Filters\{Filter, FilterMultiSelect, FilterMultiSelectAsync};

use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\Tests\Models\Category;
use PowerComponents\LivewirePowerGrid\Tests\{DishesArrayTable, DishesCollectionTable, DishesFiltersTable};

it('properly filter with category_id - Carnes selected', function (string $component) {
    $multiSelect = Filter::multiSelect('category_name', 'category_id')
        ->dataSource(Category::all())
        ->optionValue('id')
        ->optionLabel('name');

    livewire($component, [
        'testFilters' => [
            $multiSelect,
        ],
    ])
        ->set('filters', [
            'multi_select' => [
                'category_id' => [
                    1,
                ],
            ],
        ])
        ->assertDontSee('Pastel de Nata')
        ->assertDontSee('Francesinha vegana')
        ->assertSeeInOrder([
            'Peixada da chef Nábia',
            'Carne Louca',
            'Bife à Rolê',
        ]);
})->group('filters')
    ->with('filter_multi_select_themes_with_join');

it('properly filter with id using custom builder', function (string $component) {
    $multiSelect = Filter::multiSelect('category_name', 'category_id')
        ->dataSource(Category::all())
        ->optionValue('id')
        ->optionLabel('name')
        ->query(function ($builder, $values) {
            expect($values)
                ->toBe([0 => 1])
                ->and($builder)->toBeInstanceOf(\Illuminate\Database\Eloquent\Builder::class);

            return $builder->where('dishes.id', 1);
        });

    livewire($component, [
        'testFilters' => [
            $multiSelect,
        ],
    ])
        ->set('filters', [
            'multi_select' => [
                'category_id' => [
                    1,
                ],
            ],
        ])
        ->assertSee('Pastel de Nata')
        ->assertDontSee('Francesinha vegana');
})->group('filters')
    ->with('filter_multi_select_themes_with_join');

it('properly filter with id using collection & array', function (string $component) {
    $multiSelect = Filter::multiSelect('id')
        ->dataSource(collect([['id' => 1, 'value' => 1], ['id' => 2, 'value' => 2]]))
        ->optionValue('id')
        ->optionLabel('value');

    livewire($component, [
        'testFilters' => [
            $multiSelect,
        ],
    ])
        ->set('filters', [
            'multi_select' => [
                'id' => [
                    1,
                    3,
                ],
            ],
        ])
        ->assertSee('Name 1')
        ->assertSee('Name 3')
        ->assertDontSee('Name 2');
})->group('filters')
    ->with('filter_multi_select_themes_collection', 'filter_multi_select_themes_array');

it('properly filter with category_id using custom collection', function (string $component) {
    $multiSelect = Filter::multiSelect('id', 'id')
        ->dataSource(collect([['id' => 1, 'value' => 1], ['id' => 2, 'value' => 2]]))
        ->optionValue('id')
        ->optionLabel('value')
        ->collection(function ($builder, $values) {
            expect($values)
                ->toBe([0 => 1, 1 => 3])
                ->and($builder)->toBeInstanceOf(\Illuminate\Support\Collection::class);

            return $builder->whereIn('id', [1, 3]);
        });

    livewire($component, [
        'testFilters' => [
            $multiSelect,
        ],
    ])
        ->set('filters', [
            'multi_select' => [
                'id' => [
                    1,
                    3,
                ],
            ],
        ])
        ->assertSee('Name 1')
        ->assertSee('Name 3')
        ->assertDontSee('Name 2');
})->group('filters')
    ->with('filter_multi_select_themes_collection', 'themes with array table');

it('properly filter with category_id - Carnes and Peixe selected', function (string $component) {
    $multiSelect = Filter::multiSelect('category_name', 'category_id')
        ->dataSource(Category::all())
        ->optionValue('id')
        ->optionLabel('name');

    /** @var PowerGridComponent $livewire */
    $livewire = livewire($component, [
        'testFilters' => [
            $multiSelect,
        ],
    ])
        ->set('setUp.footer.perPage', '20')
        ->assertSeeHtmlInOrder([
            'x-data="pgTomSelect(JSON.parse(&#039;{\u0022tableName\u0022:\u0022default\u0022,\u0022title\u0022:\u0022Category\u0022,\u0022dataField\u0022:\u0022category_id\u0022,\u0022optionValue\u0022:\u0022id\u0022,\u0022optionLabel\u0022:\u0022name\u0022,\u0022initialValues\u0022:[],\u0022framework\u0022:{\u0022plugins\u0022:{\u0022clear_button\u0022:{\u0022title\u0022:\u0022Remove all selected options\u0022}}}}&#039;))"',
            'wire:model.defer="filters.multi_select.category_id.values"',
            'x-ref="select_picker_category_id_default"',
        ])
        ->set('filters', [
            'multi_select' => [
                'category_id' => [
                    1, // Carnes
                ],
            ],
        ])
        ->assertDontSee('Pastel de Nata')
        ->assertDontSee('борщ')
        ->assertDontSee('Francesinha vegana')
        ->assertSeeInOrder([
            'Peixada da chef Nábia',
            'Carne Louca',
            'Bife à Rolê',
        ])
        ->set('filters', [
            'multi_select' => [
                'category_id' => [
                    3, // Tortas
                    7, // Sobremesas
                ],
            ],
        ])
        ->assertSee('борщ')
        ->assertDontSee('Peixada da chef Nábia');

    $column = collect($livewire->columns)
        ->filter(fn ($column) => $column->field === 'category_name')->first();

    $categories = Category::all();

    expect($column->filters->first())
        ->dataSource->toHaveLength($categories->count())
        ->optionValue->toBe($multiSelect->optionValue)
        ->optionLabel->toBe($multiSelect->optionLabel)
        ->className->toBe(FilterMultiSelect::class)
        ->field->toBe($multiSelect->field)
        ->title->toBe($column->title);
})->group('filters')
    ->with('filter_multi_select_themes_with_join');

it('properly filter with category_id - multiple select async', function (string $component) {
    $multiSelect = Filter::multiSelectAsync('category_name', 'category_id')
        ->url('http://localhost/category')
        ->method('POST')
        ->parameters([0 => 'Luan'])
        ->optionValue('id')
        ->optionLabel('name');

    /** @var PowerGridComponent $livewire */
    $livewire = livewire($component, [
        'testFilters' => [
            $multiSelect,
        ],
    ])
        ->set('setUp.footer.perPage', '20')
        ->assertSeeHtmlInOrder([
            'wire:model.defer="filters.multi_select.category_id.values"',
            'x-ref="select_picker_category_id_default"',
        ])
        ->set('filters', [
            'multi_select' => [
                'category_id' => [
                    1, // Carnes
                ],
            ],
        ])
        ->assertDontSee('Pastel de Nata')
        ->assertDontSee('борщ')
        ->assertDontSee('Francesinha vegana')
        ->assertSeeInOrder([
            'Peixada da chef Nábia',
            'Carne Louca',
            'Bife à Rolê',
        ])
        ->set('filters', [
            'multi_select' => [
                'category_id' => [
                    3, // Tortas
                    7, // Sobremesas
                ],
            ],
        ])
        ->assertSee('борщ')
        ->assertDontSee('Peixada da chef Nábia');

    $column = collect($livewire->columns)
        ->filter(fn ($column) => $column->field === 'category_name')->first();

    expect($column->filters->first())
        ->url->toBe('http://localhost/category')
        ->method->toBe('POST')
        ->parameters->toMatchArray([0 => 'Luan'])
        ->optionValue->toBe($multiSelect->optionValue)
        ->optionLabel->toBe($multiSelect->optionLabel)
        ->className->toBe(FilterMultiSelectAsync::class)
        ->field->toBe($multiSelect->field)
        ->title->toBe($column->title);
})->group('filters')
    ->with('filter_multi_select_themes_with_join');

dataset('filter_multi_select_themes_with_join', [
    'tailwind -> id'  => [DishesFiltersTable::class, (object) ['theme' => 'tailwind', 'join' => false]],
    'bootstrap -> id' => [DishesFiltersTable::class, (object) ['theme' => 'bootstrap', 'join' => false]],
    'tailwind join'   => [DishesFiltersTable::class, (object) ['theme' => 'tailwind', 'join' => true]],
    'bootstrap join'  => [DishesFiltersTable::class, (object) ['theme' => 'bootstrap', 'join' => true]],
]);

dataset('filter_multi_select_themes_array', [
    [DishesArrayTable::class, 'tailwind'],
    [DishesArrayTable::class, 'bootstrap'],
]);

dataset('filter_multi_select_themes_collection', [
    'tailwind'  => [DishesCollectionTable::class, 'tailwind'],
    'bootstrap' => [DishesCollectionTable::class, 'bootstrap'],
]);
