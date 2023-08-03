<?php

use Illuminate\Support\Str;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

use function PowerComponents\LivewirePowerGrid\Tests\Plugins\livewire;

use PowerComponents\LivewirePowerGrid\Tests\{DishesQueryBuilderTable, DishesTable, DishesTableWithJoin};

;

it('properly filters by inputText, number, boolean filter and clearAll', function (string $component, object $params) {
    $component = livewire($component, [
        'testFilters' => [
            Filter::number('price')
                ->thousands('.')
                ->decimal(','),
            Filter::inputText('name')->operators(),
            Filter::number('price')->thousands('.')->decimal(','),
            Filter::boolean('in_stock'),
        ],
    ])
        ->call($params->theme);

    /** @var PowerGridComponent $component */
    expect($component->filters)
        ->toMatchArray([]);

    $component->set('filters', filterInputText('ba', 'contains', $params->field));

    if (str_contains($params->field, '.')) {
        $data  = Str::of($params->field)->explode('.');
        $table = $data->get(0);
        $field = $data->get(1);

        expect($component->filters)
            ->toMatchArray([
                'input_text' => [
                    $table => [
                        $field => 'ba',
                    ],
                ],
                'input_text_options' => [
                    $table => [
                        $field => 'contains',
                    ],
                ],
            ]);
    } else {
        expect($component->filters)
            ->toMatchArray([
                'input_text' => [
                    $params->field => 'ba',
                ],
                'input_text_options' => [
                    $params->field => 'contains',
                ],
            ]);
    }

    $component->assertSee('Barco-Sushi da Sueli');

    $filters = array_merge($component->filters, filterNumber('price', '80,00', '100', '.', ','));

    $component->set('filters', $filters)
        ->assertDontSee('Barco-Sushi da Sueli')
        ->assertSee('Barco-Sushi Simples')
        ->assertDontSee('Polpetone Filé Mignon')
        ->assertDontSee('борщ');

    expect($component->filters)
        ->toMatchArray($filters);

    $filters = array_merge($component->filters, filterBoolean('in_stock', 'true'));

    $component->set('filters', $filters)
       ->assertDontSee('Barco-Sushi Simples');

    expect($component->filters)
        ->toMatchArray($filters);

    $component->call('clearFilter', $params->field);

    $filters = array_merge(
        [
            'input_text'         => [],
            'input_text_options' => [],
        ],
        filterNumber('price', '80,00', '100', '.', ','),
        filterBoolean('in_stock', 'true')
    );

    expect($component->filters)
        ->toMatchArray($filters);

    $component->assertDontSee('Polpetone Filé Mignon');

    $component->call('clearAllFilters');

    $component->assertSee('Barco-Sushi da Sueli')
        ->assertSee('Barco-Sushi Simples')
        ->assertSee('Polpetone Filé Mignon')
        ->assertSee('борщ');
    expect($component->filters)
        ->toMatchArray([]);
})->group('filters')
    ->with('filter_multiple_themes_with_join', 'filter_multiple_query_builder');

dataset('filter_multiple_themes_with_join', [
    'tailwind -> id'         => [DishesTable::class, (object) ['theme' => 'tailwind', 'field' => 'name']],
    'bootstrap -> id'        => [DishesTable::class, (object) ['theme' => 'bootstrap', 'field' => 'name']],
    'tailwind -> dishes.id'  => [DishesTableWithJoin::class, (object) ['theme' => 'tailwind', 'field' => 'dishes.name']],
    'bootstrap -> dishes.id' => [DishesTableWithJoin::class, (object) ['theme' => 'bootstrap', 'field' => 'dishes.name']],
]);

dataset('filter_multiple_query_builder', [
    'tailwind query builder -> id'  => [DishesQueryBuilderTable::class, (object) ['theme' => 'tailwind', 'field' => 'id']],
    'bootstrap query builder -> id' => [DishesQueryBuilderTable::class, (object) ['theme' => 'bootstrap', 'field' => 'id']],
]);
