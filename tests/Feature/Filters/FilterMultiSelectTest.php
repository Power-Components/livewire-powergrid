<?php

use function Pest\Livewire\livewire;

use PowerComponents\LivewirePowerGrid\Filters\{Filter, FilterMultiSelect, FilterMultiSelectAsync};

use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\Tests\DishesFiltersTable;
use PowerComponents\LivewirePowerGrid\Tests\Models\Category;

it('properly filter with category_id - Carnes selected', function (string $component) {
    livewire($component)
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
})->group('filters')->with('multi_select');

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
        ->assertDontSee('Empadão de Palmito')
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
        ->assertSee('Empadão de Palmito')
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
})->group('filters')->with('multi_select');

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
        ->assertDontSee('Empadão de Palmito')
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
        ->assertSee('Empadão de Palmito')
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
})->group('filters')->with('multi_select');

dataset('multi_select', [
    'tailwind -> id'  => [DishesFiltersTable::class, (object) ['theme' => 'tailwind', 'join' => false]],
    'bootstrap -> id' => [DishesFiltersTable::class, (object) ['theme' => 'bootstrap', 'join' => false]],
    'tailwind join'   => [DishesFiltersTable::class, (object) ['theme' => 'tailwind', 'join' => true]],
    'bootstrap join'  => [DishesFiltersTable::class, (object) ['theme' => 'bootstrap', 'join' => true]],
]);
