<?php

use PowerComponents\LivewirePowerGrid\Components\Filters\{FilterInputText};
use PowerComponents\LivewirePowerGrid\Facades\Filter;

use function PowerComponents\LivewirePowerGrid\Tests\Plugins\livewire;

use PowerComponents\LivewirePowerGrid\Tests\{DishTableBase, DishesFiltersTable, DishesQueryBuilderTable};

;

it('properly filters by "name is"', function (string $component, object $params) {
    $filter   = Filter::inputText('name', $params->field)->operators();
    $livewire = livewire($component, [
        'join'        => $params->join,
        'testFilters' => [
            $filter,
        ],
    ])
        ->call($params->theme)
        ->set('filters', filterInputText('Francesinha', 'is', $params->field))
        ->assertSee('Francesinha')
        ->assertDontSee('Francesinha vegana')
        ->call('clearFilter', $params->field)
        ->assertSee('Francesinha vegana');

    expectColumnsFilterMatch($livewire, $filter);
})->group('filters', 'filterInputText')
    ->with('filter_input_text_options_model_themes_with_join', 'filter_input_text_options_query_builder');

it('properly filters by "name is" using nonexistent record', function (string $component, object $params) {
    $filter = Filter::inputText('name', $params->field)
        ->operators();
    $livewire = livewire($component, [
        'join'        => $params->join,
        'testFilters' => [
            $filter,
        ],
    ])
        ->call($params->theme)
        ->set('filters', filterInputText('Nonexistent dish', 'is', $params->field))
        ->assertSee('No records found')
        ->assertDontSee('Francesinha');

    expectColumnsFilterMatch($livewire, $filter);
})->group('filters', 'filterInputText')->with('filter_input_text_options_model_themes_with_join', 'filter_input_text_options_query_builder');

it('properly show placeholder', function (string $component, object $params) {
    $filter = Filter::inputText('name', $params->field)
        ->placeholder('Test Placeholder');

    livewire($component, [
        'join'        => $params->join,
        'testFilters' => [
            $filter,
        ],
    ])
        ->call($params->theme)
        ->assertSeeHtml('placeholder="Test Placeholder"');
})->group('filters', 'filterInputText')->with('filter_input_text_options_model_themes_with_join', 'filter_input_text_options_query_builder');

it('properly filters by "name is not"', function (string $component, object $params) {
    $component = livewire($component, [
        'join' => $params->join,
    ])
        ->call($params->theme)
        ->set('testFilters', [
            Filter::inputText('name')->operators(),
        ])
        ->set('filters', filterInputText('Francesinha vegana', 'is_not', $params->field))
        ->assertSee('Francesinha')
        ->assertDontSee('Francesinha vegana');

    expectInputText($params, $component, 'Francesinha vegana', 'is_not');

    $component->call('clearFilter', $params->field)
        ->assertSee('Francesinha vegana');

    expect($component->filters)
        ->toMatchArray([
            'input_text'         => [],
            'input_text_options' => [],
        ]);
})->group('filters', 'filterInputText')->with('filter_input_text_options_model_themes_with_join', 'filter_input_text_options_query_builder');

it('properly filters by "name is not" using nonexistent record', function (string $component, object $params) {
    $component = livewire($component, [
        'join' => $params->join,
    ])
        ->call($params->theme)
        ->set('filters', filterInputText('Nonexistent dish', 'is_not', $params->field))
        ->assertSee('Francesinha')
        ->assertSee('Francesinha vegana');

    expectInputText($params, $component, 'Nonexistent dish', 'is_not');

    $component->call('clearFilter', $params->field);

    expect($component->filters)
        ->toMatchArray([
            'input_text'         => [],
            'input_text_options' => [],
        ]);
})->group('filters', 'filterInputText')->with('filter_input_text_options_model_themes_with_join', 'filter_input_text_options_query_builder');

it('properly filters by "name contains"', function (string $component, object $params) {
    $component = livewire($component, [
        'join' => $params->join,
    ])
        ->call($params->theme)
        ->set('filters', filterInputText('francesinha', 'contains', $params->field))
        ->assertSee('Francesinha')
        ->assertSee('Francesinha vegana');

    expectInputText($params, $component, 'francesinha', 'contains');

    $component->call('clearFilter', $params->field);

    expect($component->filters)
        ->toMatchArray([
            'input_text'         => [],
            'input_text_options' => [],
        ]);
})->group('filters', 'filterInputText')->with('filter_input_text_options_model_themes_with_join', 'filter_input_text_options_query_builder');

it('properly filters by "name contains" using nonexistent record', function (string $component, object $params) {
    $component = livewire($component, [
        'join' => $params->join,
    ])
        ->call($params->theme)
        ->set('testFilters', [
            Filter::inputText('name')->operators(),
        ])
        ->set('filters', filterInputText('Nonexistent dish', 'contains', $params->field))
        ->assertSee('No records found')
        ->assertDontSee('Francesinha')
        ->assertDontSee('Francesinha vegana');

    expectInputText($params, $component, 'Nonexistent dish', 'contains');

    $component->call('clearFilter', $params->field);

    expect($component->filters)
        ->toMatchArray([
            'input_text'         => [],
            'input_text_options' => [],
        ]);
})->group('filters', 'filterInputText')->with('filter_input_text_options_model_themes_with_join', 'filter_input_text_options_query_builder');

it('properly filters by "name contains not"', function (string $component, object $params) {
    $component = livewire($component, [
        'join' => $params->join,
    ])
        ->call($params->theme)
        ->set('testFilters', [
            Filter::inputText('name')->operators(),
        ])
        ->set('filters', filterInputText('francesinha', 'contains_not', $params->field))
        ->assertDontSee('Francesinha')
        ->assertDontSee('Francesinha vegana');

    expectInputText($params, $component, 'francesinha', 'contains_not');

    $component->call('clearFilter', $params->field);

    expect($component->filters)
        ->toMatchArray([
            'input_text'         => [],
            'input_text_options' => [],
        ]);
})->group('filters', 'filterInputText')->with('filter_input_text_options_model_themes_with_join', 'filter_input_text_options_query_builder');

it('properly filters by "name contains not" using nonexistent record', function (string $component, object $params) {
    $component = livewire($component, [
        'join' => $params->join,
    ])
        ->call($params->theme)
        ->set('testFilters', [
            Filter::inputText('name')->operators(),
        ])
        ->set('filters', filterInputText('Nonexistent dish', 'contains_not', $params->field))
        ->assertSee('Francesinha')
        ->assertSee('Francesinha vegana');

    expectInputText($params, $component, 'Nonexistent dish', 'contains_not');

    $component->call('clearFilter', $params->field);

    expect($component->filters)
        ->toMatchArray([
            'input_text'         => [],
            'input_text_options' => [],
        ]);
})->group('filters', 'filterInputText')->with('filter_input_text_options_model_themes_with_join', 'filter_input_text_options_query_builder');

it('properly filters by "name starts with"', function (string $component, object $params) {
    $component = livewire($component, [
        'join' => $params->join,
    ])
        ->call($params->theme)
        ->set('testFilters', [
            Filter::inputText('name')->operators(),
        ])
        ->set('filters', filterInputText('fran', 'starts_with', $params->field))
        ->assertSee('Francesinha')
        ->assertSee('Francesinha vegana')
        ->assertDontSee('Barco-Sushi da Sueli');

    expectInputText($params, $component, 'fran', 'starts_with');

    $component->call('clearFilter', $params->field);

    expect($component->filters)
        ->toMatchArray([
            'input_text'         => [],
            'input_text_options' => [],
        ]);
})->group('filters', 'filterInputText')->with('filter_input_text_options_model_themes_with_join', 'filter_input_text_options_query_builder');

it('properly filters by "name starts with" using nonexistent record', function (string $component, object $params) {
    $component = livewire($component, [
        'join' => $params->join,
    ])
        ->call($params->theme)
        ->set('testFilters', [
            Filter::inputText('name')->operators(),
        ])
        ->set('filters', filterInputText('Nonexistent', 'starts_with', $params->field))
        ->assertSee('No records found')
        ->assertDontSee('Francesinha')
        ->assertDontSee('Francesinha vegana')
        ->assertDontSee('Barco-Sushi da Sueli');

    expectInputText($params, $component, 'Nonexistent', 'starts_with');

    $component->call('clearFilter', $params->field);

    expect($component->filters)
        ->toMatchArray([
            'input_text'         => [],
            'input_text_options' => [],
        ]);
})->group('filters', 'filterInputText')->with('filter_input_text_options_model_themes_with_join', 'filter_input_text_options_query_builder');

it('properly filters by "name ends with"', function (string $component, object $params) {
    $component = livewire($component, [
        'join' => $params->join,
    ])
        ->call($params->theme)
        ->set('testFilters', [
            Filter::inputText('name')->operators(),
        ])
        ->set('filters', filterInputText('vegana', 'ends_with', $params->field))
        ->assertSee('Francesinha vegana')
        ->assertDontSee('Barco-Sushi da Sueli');

    expectInputText($params, $component, 'vegana', 'ends_with');

    $component->call('clearFilter', $params->field);

    expect($component->filters)
        ->toMatchArray([
            'input_text'         => [],
            'input_text_options' => [],
        ]);
})->group('filters', 'filterInputText')->with('filter_input_text_options_model_themes_with_join', 'filter_input_text_options_query_builder');

it('properly filters by "name ends with" using nonexistent record', function (string $component, object $params) {
    $component = livewire($component, [
        'join' => $params->join,
    ])
        ->call($params->theme)
        ->set('testFilters', [
            Filter::inputText('name')->operators(),
        ])
        ->set('filters', filterInputText('Nonexistent', 'ends_with', $params->field))
        ->assertSee('No records found')
        ->assertDontSee('Francesinha')
        ->assertDontSee('Francesinha vegana')
        ->assertDontSee('Barco-Sushi da Sueli');

    expectInputText($params, $component, 'Nonexistent', 'ends_with');

    $component->call('clearFilter', $params->field);

    expect($component->filters)
        ->toMatchArray([
            'input_text'         => [],
            'input_text_options' => [],
        ]);
})->group('filters', 'filterInputText')->with('filter_input_text_options_model_themes_with_join', 'filter_input_text_options_query_builder');

it('properly filters by "chef name is blank"', function (string $component, object $params) {
    $component = livewire($component, [
        'join' => $params->join,
    ])
        ->call($params->theme)
        ->set('testFilters', [
            Filter::inputText('name')->operators(),
        ])
        ->set('filters', filterInputText('', 'is_blank', 'chef_name'))
        ->assertSee('Carne Louca')
        ->assertDontSee('Pastel de Nata')
        ->assertDontSee('Francesinha vegana')
        ->set('setUp.footer.perPage', '50')
        ->assertSee('Carne Louca')
        ->assertDontSee('Pastel de Nata')
        ->assertDontSee('Francesinha vegana');

    expect($component->filters)
        ->toMatchArray([
            'input_text' => [
                'chef_name' => '',
            ],
            'input_text_options' => [
                'chef_name' => 'is_blank',
            ],
        ]);

    $component->call('clearFilter', 'chef_name');

    expect($component->filters)
        ->toMatchArray([
            'input_text'         => [],
            'input_text_options' => [],
        ]);
})->group('filters', 'filterInputText')->with('filter_input_text_options_model_themes_with_join', 'filter_input_text_options_query_builder');

it('properly filters by "chef name is NOT blank"', function (string $component, object $params) {
    $component = livewire($component, [
        'join' => $params->join,
    ])
        ->call($params->theme)
        ->set('testFilters', [
            Filter::inputText('name')->operators(),
        ])
        ->set('filters', filterInputText('', 'is_not_blank', 'chef_name'))
        ->assertSee('Pastel de Nata')
        ->assertSee('Francesinha vegana')
        ->assertDontSee('Carne Louca')
        ->set('setUp.footer.perPage', '50')
        ->assertSee('Pastel de Nata')
        ->assertSee('Francesinha vegana')
        ->assertDontSee('Carne Louca');

    expect($component->filters)
        ->toMatchArray([
            'input_text' => [
                'chef_name' => '',
            ],
            'input_text_options' => [
                'chef_name' => 'is_not_blank',
            ],
        ]);

    $component->call('clearFilter', 'chef_name');

    expect($component->filters)
        ->toMatchArray([
            'input_text'         => [],
            'input_text_options' => [],
        ]);
})->group('filters', 'filterInputText')->with('filter_input_text_options_model_themes_with_join', 'filter_input_text_options_query_builder');

it('properly filters by "chef name is null"', function (string $component, object $params) {
    $component = livewire($component, [
        'join' => $params->join,
    ])
        ->call($params->theme)
        ->set('testFilters', [
            Filter::inputText('name')->operators(),
        ])
        ->set('filters', filterInputText('', 'is_null', 'chef_name'))
        ->assertSee('Pastel de Nata')
        ->assertDontSee('Francesinha vegana')
        ->assertDontSee('Carne Louca')
        ->set('setUp.footer.perPage', '50')
        ->assertSee('Pastel de Nata')
        ->assertDontSee('Francesinha vegana')
        ->assertDontSee('Carne Louca');

    expect($component->filters)
        ->toMatchArray([
            'input_text' => [
                'chef_name' => '',
            ],
            'input_text_options' => [
                'chef_name' => 'is_null',
            ],
        ]);

    $component->call('clearFilter', 'chef_name');

    expect($component->filters)
        ->toMatchArray([
            'input_text'         => [],
            'input_text_options' => [],
        ]);
})->group('filters', 'filterInputText')->with('filter_input_text_options_model_themes_with_join', 'filter_input_text_options_query_builder');

it('properly filters by "chef name is NOT null"', function (string $component, object $params) {
    $filter   = Filter::inputText('name', 'chef_name')->operators();
    $livewire = livewire($component, [
        'join'        => $params->join,
        'testFilters' => [
            $filter,
        ],
    ])
        ->call($params->theme)
        ->set('filters', filterInputText('', 'is_not_null', 'chef_name'))
        ->assertSee('Francesinha vegana')
        ->assertSee('Carne Louca')
        ->assertDontSee('Pastel de Nata')
        ->set('setUp.footer.perPage', '50')
        ->assertSee('Francesinha vegana')
        ->assertSee('Carne Louca')
        ->assertDontSee('Pastel de Nata');

    expect($livewire->filters)
        ->toMatchArray([
            'input_text' => [
                'chef_name' => '',
            ],
            'input_text_options' => [
                'chef_name' => 'is_not_null',
            ],
        ]);

    $livewire->call('clearFilter', 'is_not_null');

    expect($livewire->filters)
        ->toMatchArray([]);

    expectColumnsFilterMatch($livewire, $filter);
})->group('filters', 'filterInputText')->with('filter_input_text_options_model_themes_with_join', 'filter_input_text_options_query_builder');

it('properly filters by "chef name is empty"', function (string $component, object $params) {
    $filter   = Filter::inputText('name', $params->field)->operators();
    $livewire = livewire($component, [
        'join'        => $params->join,
        'testFilters' => [
            $filter,
        ],
    ])
        ->call($params->theme)
        ->set('filters', filterInputText('', 'is_empty', 'chef_name'))
        ->assertSee('Pastel de Nata')
        ->assertSee('Carne Louca')
        ->assertDontSee('Francesinha vegana')
        ->set('setUp.footer.perPage', '50')
        ->assertSee('Pastel de Nata')
        ->assertSee('Carne Louca')
        ->assertDontSee('Francesinha vegana');

    expect($livewire->filters)
        ->toMatchArray([
            'input_text' => [
                'chef_name' => '',
            ],
            'input_text_options' => [
                'chef_name' => 'is_empty',
            ],
        ]);

    $livewire->call('clearFilter', 'is_empty');

    expect($livewire->filters)
        ->toMatchArray([]);

    expectColumnsFilterMatch($livewire, $filter);
})->group('filters', 'filterInputText')->with('filter_input_text_options_model_themes_with_join', 'filter_input_text_options_query_builder');

it('properly filters by "chef name is NOT empty"', function (string $component, object $params) {
    $filter   = Filter::inputText('name', $params->field)->operators();
    $livewire = livewire($component, [
        'join'        => $params->join,
        'testFilters' => [
            $filter,
        ],
    ])
        ->call($params->theme)
        ->set('filters', filterInputText('', 'is_not_empty', 'chef_name'))
        ->assertSee('Francesinha vegana')
        ->assertDontSee('Pastel de Nata')
        ->assertDontSee('Carne Louca')
        ->assertSee('Francesinha vegana')
        ->assertDontSee('Pastel de Nata')
        ->assertDontSee('Carne Louca');

    expectColumnsFilterMatch($livewire, $filter);
})->group('filters', 'filterInputText')->with('filter_input_text_options_model_themes_with_join', 'filter_input_text_options_query_builder');

$component = new class () extends DishTableBase {
    public function filters(): array
    {
        return [
            Filter::inputText('id')
                ->builder(function ($builder, $values) {
                    return $builder->where('dishes.id', 1);
                }),
        ];
    }
};

it('properly filters using custom builder', function (string $component, object $params) {
    $component = livewire($component, [
        'join' => $params->join,
    ])
        ->call($params->theme);

    $component->set('filters', filterInputText('francesinha', 'contains', 'id'))
        ->assertSee('Pastel de Nata')
        ->assertDontSee('Francesinha')
        ->assertDontSee('Francesinha vegana');
})->group('filters', 'filterInputText')
    ->with([
        'tailwind'  => [$component::class, (object) ['theme' => 'tailwind', 'join' => false]],
        'bootstrap' => [$component::class, (object) ['theme' => 'bootstrap', 'join' => false]],
    ]);

dataset('filter_input_text_options_model_themes_with_join', [

    'tailwind join'  => [DishesFiltersTable::class, (object) ['theme' => 'tailwind', 'field' => 'dishes.name', 'join' => true]],
    'bootstrap join' => [DishesFiltersTable::class, (object) ['theme' => 'bootstrap', 'field' => 'dishes.name', 'join' => true]],
]);

dataset('filter_input_text_options_query_builder', [
    'tailwind query builder -> id'  => [DishesQueryBuilderTable::class, (object) ['theme' => 'tailwind', 'field' => 'id']],
    'bootstrap query builder -> id' => [DishesQueryBuilderTable::class, (object) ['theme' => 'bootstrap', 'field' => 'id']],
]);

function expectColumnsFilterMatch($component, $filter, $field = 'name'): void
{
    $column = collect($component->columns)
        ->filter(fn ($column) => $column->field === $field)->first();

    expect($column->filters->first())
        ->className->toBe(FilterInputText::class)
        ->operators->toBeArray()
        ->field->toBe($filter->field)
        ->title->toBe($column->title);
}
