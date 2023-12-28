<?php

use PowerComponents\LivewirePowerGrid\Facades\Filter;

use function PowerComponents\LivewirePowerGrid\Tests\Plugins\livewire;

use PowerComponents\LivewirePowerGrid\Tests\{DishesArrayTable,
    DishesCollectionTable,
    DishesQueryBuilderTable,
    DishesTable,
    DishesTableWithJoin};

it('properly filter the produced_at field between two dates', function (string $component, object $params) {
    livewire($component, [
        'testFilters' => [
            Filter::datepicker('produced_at'),
        ],
    ])
        ->call($params->theme)
        ->set('filters', filterDate('produced_at', ['2021-02-02', '2021-04-04']))
        ->assertSee('Peixada da chef Nábia')
        ->assertSee('Carne Louca')
        ->assertSee('Bife à Rolê')
        ->assertDontSeeHtml('Francesinha vegana');
})->group('filters')
    ->skipOnSQLite()
    ->with('filter_datetime_themes_with_join', 'filter_datetime_query_builder');

it('properly filters by "between date"', function (string $component, object $params) {
    $datepicker = Filter::datepicker('produced_at_formatted', 'produced_at');

    livewire($component, [
        'testFilters' => [$datepicker],
    ])
        ->call($params->theme)
        ->set('filters', filterDate('produced_at', ['2021-01-01', '2021-03-03']))
        ->assertSeeHtmlInOrder([
            'id="input_produced_at_formatted"',
            'type="text"',
            'placeholder="Select a period"',
        ])
        ->assertSee('Pastel de Nata')
        ->assertSee('Peixada da chef Nábia')
        ->assertSee('Carne Louca')
        ->assertDontSee('Barco-Sushi Simples')
        ->set('filters', filterDateTime('produced_at', ['2021-04-04', '2021-07-07 19:59:58']))
        ->assertSee('Bife à Rolê')
        ->assertSee('Francesinha vegana')
        ->assertSee('Francesinha')
        ->assertDontSeeHtml('Barco-Sushi da Sueli');
})->group('filters', 'filterDatePicker')
    ->skipOnSQLite()
    ->with('filter_datetime_themes_with_join', 'filter_datetime_query_builder');

it('properly filters by "between date" using incorrect filter', function (string $component, object $params) {
    livewire($component)
        ->call($params->theme)
        ->set('testFilters', [
            Filter::datepicker('produced_at_formatted', 'produced_at'),
        ])
        ->set('filters', filterDate('dishes.produced_at', ['2021-03-03', '2021-03-01']))
        ->assertSee('No records found');
})->group('filters', 'filterDatePicker')
    ->with('filter_datetime_themes_with_join', 'filter_datetime_query_builder');

it('properly filter the created_at field between two dates using collection & array table', function (string $component, string $theme) {
    livewire($component, [
        'testFilters' => [
            Filter::datepicker('produced_at'),
        ],
    ])
        ->call($theme)
        ->set('filters', filterDateTime('created_at', ['2021-01-01 00:00:00', '2021-04-04 00:00:00']))
        ->assertSeeText('Name 1')
        ->assertSeeText('Name 2')
        ->assertSeeText('Name 3')
        ->assertSeeText('Name 4')
        ->assertDontSeeHtml('Name 5');
})->group('filters')
    ->with('filter_datetime_themes_collection', 'filter_datetime_themes_array');

$customCollection = new class () extends DishesCollectionTable {
    public int $dishId;

    public function filters(): array
    {
        return [
            Filter::datepicker('created_at')
                ->collection(function ($collection, $values) {
                    return $collection->where('id', 1);
                }),
        ];
    }
};

it('property filter the created_at field when we are using custom builder collection & array table', function (string $component, string $theme) {
    $dateToFilter = ['2021-01-01 00:00:00', '2021-04-04 00:00:00'];

    livewire($component)
        ->call($theme)
        ->set('filters', filterDateTime('created_at', $dateToFilter))
        ->assertSeeText('Name 1')
        ->assertDontSeeText('Name 2')
        ->assertDontSeeText('Name 3')
        ->assertDontSeeText('Name 4')
        ->assertDontSeeText('Name 5');
})->group('filters')
    ->with([
        'tailwind -> id'  => [$customCollection::class, 'tailwind'],
        'bootstrap -> id' => [$customCollection::class, 'bootstrap'],
    ]);

it('properly filter the produced_at field between another two dates', function (string $component, object $params) {
    livewire($component, [
        'testFilters' => [
            Filter::datepicker('produced_at'),
        ],
    ])
        ->call($params->theme)
        ->set('filters', filterDate('produced_at', ['2021-11-11 00:00:00', '2021-12-31 00:00:00']))
        ->assertDontSee('Peixada da chef Nábia')
        ->assertDontSee('Carne Louca')
        ->assertDontSee('Bife à Rolê');
})->group('filters')
    ->with('filter_datetime_themes_with_join');

$customBuilder = new class () extends DishesTable {
    public int $dishId;

    public function filters(): array
    {
        return [
            Filter::datepicker('produced_at')
                ->builder(function ($builder, $values) {
                    return $builder->where('dishes.id', 1);
                }),
        ];
    }
};

it('properly filter the produced_at field between another two dates - custom builder', function (string $component, string $theme) {
    $dateToFilter = ['2021-11-11 00:00:00', '2021-12-31 00:00:00'];
    livewire($component)
        ->call($theme)
        ->set('filters', filterDate('produced_at', $dateToFilter))
        ->assertSee('Pastel de Nata')
        ->assertDontSee('Peixada da chef Nábia')
        ->assertDontSee('Carne Louca')
        ->assertDontSee('Bife à Rolê');
})->group('filters')
    ->with([
        'tailwind -> id'  => [$customBuilder::class, 'tailwind'],
        'bootstrap -> id' => [$customBuilder::class, 'bootstrap'],
    ]);

dataset('filter_datetime_themes_with_join', [
    'tailwind -> id'         => [DishesTable::class, (object) ['theme' => 'tailwind', 'field' => 'id']],
    'bootstrap -> id'        => [DishesTable::class, (object) ['theme' => 'bootstrap', 'field' => 'id']],
    'tailwind -> dishes.id'  => [DishesTableWithJoin::class, (object) ['theme' => 'tailwind', 'field' => 'dishes.id']],
    'bootstrap -> dishes.id' => [DishesTableWithJoin::class, (object) ['theme' => 'bootstrap', 'field' => 'dishes.id']],
]);

dataset('filter_datetime_query_builder', [
    'tailwind query builder -> id'  => [DishesQueryBuilderTable::class, (object) ['theme' => 'tailwind', 'field' => 'id']],
    'bootstrap query builder -> id' => [DishesQueryBuilderTable::class, (object) ['theme' => 'bootstrap', 'field' => 'id']],
]);

dataset('filter_datetime_themes_array', [
    [DishesArrayTable::class, 'tailwind'],
    [DishesArrayTable::class, 'bootstrap'],
]);

dataset('filter_datetime_themes_collection', [
    'tailwind'  => [DishesCollectionTable::class, 'tailwind'],
    'bootstrap' => [DishesCollectionTable::class, 'bootstrap'],
]);

function filterDate(string $dataField, array $value): array
{
    return [
        'date' => [
            $dataField => [
                'start' => $value[0],
                'end'   => $value[1],
            ],
        ],
    ];
}

function filterDateTime(string $dataField, array $value): array
{
    return [
        'datetime' => [
            $dataField => [
                'start' => $value[0],
                'end'   => $value[1],
            ],
        ],
    ];
}
