<?php

use PowerComponents\LivewirePowerGrid\Facades\Filter;

use function PowerComponents\LivewirePowerGrid\Tests\Plugins\livewire;

use PowerComponents\LivewirePowerGrid\Tests\{DishesArrayTable,
    DishesCollectionTable,
    DishesQueryBuilderTable,
    DishesTable,
    DishesTableWithJoin};

;

it('properly renders the number filter', function (string $component, object $params) {
    $number = Filter::number('id', $params->field)
        ->thousands('.')
        ->decimal(',');

    livewire($component, [
        'testFilters' => [$number],
    ])
        ->call($params->theme)
        ->set('filters', filterNumber($params->field, '2', null))
        ->assertSee('Peixada da chef Nábia')
        ->assertSee('Francesinha')
        ->assertSee('борщ')
        ->assertDontSee('Pastel de Nata');
})->group('filters', 'filterNumber')
    ->with('filter_number_themes_with_join', 'filter_number_query_builder');

$customBuilder = new class () extends DishesTable {
    public int $dishId;

    public function filters(): array
    {
        return [
            Filter::number('id')
                ->builder(function ($builder, $values) {
                    expect($values)->toBe([
                        'start' => '2',
                        'end'   => null,
                    ])
                        ->and($builder)->toBeInstanceOf(\Illuminate\Database\Eloquent\Builder::class);

                    return $builder->where('dishes.id', 2);
                })
                ->thousands('.')
                ->decimal(','),
        ];
    }
};

it('properly renders the number filter - custom builder', function (string $component, object $params) {
    livewire($component)
        ->call($params->theme)
        ->set('filters', filterNumber('id', '2', null))
        ->assertSee('Peixada da chef Nábia')
        ->assertDontSee('Francesinha')
        ->assertDontSee('борщ')
        ->assertDontSee('Pastel de Nata');
})->group('filters', 'filterNumber')
    ->with([
        'tailwind -> id'  => [$customBuilder::class, (object) ['theme' => 'tailwind']],
        'bootstrap -> id' => [$customBuilder::class, (object) ['theme' => 'bootstrap']],
    ]);

it('properly filters by "min"', function (string $component, object $params) {
    livewire($component)
        ->call($params->theme)
        ->set('testFilters', [
            Filter::number('price_BRL', 'price'),
        ])
        ->set('filters', filterNumber($params->field, '2', null))
        ->assertSee('Peixada da chef Nábia')
        ->assertSee('Francesinha')
        ->assertSee('борщ')
        ->assertDontSee('Pastel de Nata');
})->group('filters', 'filterNumber')
    ->with('filter_number_themes_with_join', 'filter_number_query_builder');

it('properly filters by "min" - using collection', function (string $component, string $theme) {
    livewire($component)
        ->call($theme)
        ->set('testFilters', [
            Filter::number('price_BRL', 'price'),
        ])
        ->set('filters', filterNumber('price', 1.68, null))
        ->assertSee('Name 2')
        ->assertSee('Name 3')
        ->assertSee('Name 4')
        ->assertDontSee('Name 1');
})->group('filters', 'filterNumber')
    ->with('filter_number_themes_collection', 'filter_number_themes_array');

it('properly filters by "max"', function (string $component, object $params) {
    livewire($component)
        ->call($params->theme)
        ->set('testFilters', [
            Filter::number('price_BRL', 'price'),
        ])
        ->set('filters', filterNumber($params->field, null, '3'))
        ->assertSee('Pastel de Nata')
        ->assertSee('Peixada da chef Nábia')
        ->assertSee('Carne Louca')
        ->assertDontSee('Bife à Rolê');
})->group('filters', 'filterNumber')
    ->with('filter_number_themes_with_join');

it('properly filters by "max" - using collection & array table', function (string $component, string $theme) {
    livewire($component)
        ->call($theme)
        ->set('testFilters', [
            Filter::number('price_BRL', 'price'),
        ])
        ->set('filters', filterNumber('price', null, 1.68))
        ->assertSee('Name 1')
        ->assertSee('Name 2')
        ->assertDontSee('Name 3');
})->group('filters', 'filterNumber')
    ->with('filter_number_themes_collection', 'filter_number_themes_array');

it('properly filters by "min & max"', function (string $component, object $params) {
    livewire($component)
        ->call($params->theme)
        ->set('testFilters', [
            Filter::number('price_BRL', 'price'),
        ])
        ->set('filters', filterNumber($params->field, '1', '2'))
        ->assertSee('Pastel de Nata')
        ->assertSee('Peixada da chef Nábia')
        ->assertDontSee('Carne Louca');
})->group('filters', 'filterNumber')
    ->with('filter_number_themes_with_join');

it('properly filters by "min & max" - using collection & array', function (string $component, string $theme) {
    livewire($component)
        ->call($theme)
        ->set('testFilters', [
            Filter::number('price'),
        ])
        ->set('filters', filterNumber('price', 1.68, 1.78))
        ->assertSee('Name 2')
        ->assertSee('Name 3')
        ->assertDontSee('Name 1')
        ->assertDontSee('Name 4')
        ->assertDontSee('Name 5');
})->group('filters', 'filterNumber')
    ->with('filter_number_themes_collection', 'filter_number_themes_array');

it('properly filters by "min & max" currency', function (string $component, object $params) {
    livewire($component)
        ->call($params->theme)
        ->set('testFilters', [
            Filter::number('price'),
        ])
        ->set('filters', filterNumber('price', '60.49', '100'))
        ->assertSee('Francesinha')
        ->assertSee('Barco-Sushi da Sueli')
        ->assertSee('Barco-Sushi Simples')
        ->assertSee('Polpetone Filé Mignon')
        ->assertDontSee('борщ');
})->group('filters', 'filterNumber')
    ->with('filter_number_themes_with_join', 'filter_number_query_builder');

it('ignores null "min & max"', function (string $component, object $params) {
    livewire($component)
        ->call($params->theme)
        ->set('testFilters', [
            Filter::number('price'),
        ])
        ->set('filters', filterNumber('price', null, null))
        ->assertSee('Pastel de Nata')
        ->assertSee('Peixada da chef Nábia')
        ->assertSee('борщ');
})->group('filters', 'filterNumber')
    ->with('filter_number_themes_with_join', 'filter_number_query_builder');

it('ignores null "min & max" - using collection', function (string $component, string $theme) {
    livewire($component)
        ->call($theme)
        ->set('testFilters', [
            Filter::number('price'),
        ])
        ->set('filters', filterNumber('price', null, null))
        ->assertSee('Name 1')
        ->assertSee('Name 2')
        ->assertSee('Name 3')
        ->assertSee('Name 4')
        ->assertSee('Name 5');
})->group('filters', 'filterNumber')
    ->with('filter_number_themes_collection', 'filter_number_themes_array');

it('displays "No records found" with non-existent min', function (string $component, object $params) {
    livewire($component)
        ->call($params->theme)
        ->set('testFilters', [
            Filter::number('price'),
        ])
        ->set('filters', filterNumber($params->field, '1000000', null))
        ->assertSee('No records found')
        ->assertDontSee('Pastel de Nata');
})->group('filters', 'filterNumber')->with('filter_number_themes_with_join');

it('displays "No records found" with non-existent min - using collection & array table', function (string $component, string $theme) {
    livewire($component)
        ->call($theme)
        ->set('testFilters', [
            Filter::number('price'),
        ])
        ->set('filters', filterNumber('price', '1000000', null))
        ->assertSee('No records found')
        ->assertDontSee('Name 1');
})->group('filters', 'filterNumber')->with('filter_number_themes_collection', 'filter_number_themes_array');

it('properly filters by "min & max" formatted', function (string $component, object $params) {
    $instance = livewire($component)
        ->call($params->theme)
        ->set('testFilters', [
            Filter::number('price')
                ->thousands('.')
                ->decimal('.'),
        ]);

    $instance->set('filters', filterNumber('price', '1.50', '20.51'))
        ->assertSee('Pastel de Nata')
        ->assertSee('Peixada da chef Nábia')
        ->assertDontSee('Carne Louca');
})->group('filters', 'filterNumber')->with('filter_number_themes_with_join');

dataset('filter_number_themes_with_join', [
    'tailwind -> id'         => [DishesTable::class, (object) ['theme' => 'tailwind', 'field' => 'id']],
    'bootstrap -> id'        => [DishesTable::class, (object) ['theme' => 'bootstrap', 'field' => 'id']],
    'tailwind -> dishes.id'  => [DishesTableWithJoin::class, (object) ['theme' => 'tailwind', 'field' => 'dishes.id']],
    'bootstrap -> dishes.id' => [DishesTableWithJoin::class, (object) ['theme' => 'bootstrap', 'field' => 'dishes.id']],
]);

dataset('filter_number_query_builder', [
    'tailwind query builder -> id'  => [DishesQueryBuilderTable::class, (object) ['theme' => 'tailwind', 'field' => 'id']],
    'bootstrap query builder -> id' => [DishesQueryBuilderTable::class, (object) ['theme' => 'bootstrap', 'field' => 'id']],
]);

dataset('filter_number_themes_array', [
    [DishesArrayTable::class, 'tailwind'],
    [DishesArrayTable::class, 'bootstrap'],
]);

dataset('filter_number_themes_collection', [
    'tailwind'  => [DishesCollectionTable::class, 'tailwind'],
    'bootstrap' => [DishesCollectionTable::class, 'bootstrap'],
]);
