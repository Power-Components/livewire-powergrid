<?php

use function Pest\Livewire\livewire;

use PowerComponents\LivewirePowerGrid\Tests\{DishesQueryBuilderTable, DishesTable, DishesTableWithJoin};

it('sorts by "name" and then by "id"', function (string $component, object $params) {
    livewire($component)
        ->call($params->theme)
        ->assertSeeHtml('Pastel de Nata')
        ->call('sortBy', $params->field)
        ->assertSeeHtml('Almôndegas ao Sugo')
        ->assertDontSeeHtml('Pastel de Nata')
        ->call('sortBy', 'id')
        ->assertSeeHtml('Pastel de Nata');
})->with('column_join', 'column_query_builder');

it('searches data', function (string $component, object $params) {
    livewire($component)
        ->call($params->theme)
        ->assertSeeHtml('Pastel de Nata')
        ->set('search', 'Sugo')
        ->assertDontSee('Pastel de Nata');
})->with('column_join', 'column_query_builder');

dataset('column_join', [
    'tailwind'       => [DishesTable::class, (object) ['theme' => 'tailwind', 'field' => 'name']],
    'bootstrap'      => [DishesTable::class, (object) ['theme' => 'bootstrap', 'field' => 'name']],
    'tailwind join'  => [DishesTableWithJoin::class, (object) ['theme' => 'tailwind', 'field' => 'dishes.name']],
    'bootstrap join' => [DishesTableWithJoin::class, (object) ['theme' => 'bootstrap', 'field' => 'dishes.name']],
]);

dataset('column_query_builder', [
    'tailwind query builder -> id'  => [DishesQueryBuilderTable::class, (object) ['theme' => 'tailwind', 'field' => 'id']],
    'bootstrap query builder -> id' => [DishesQueryBuilderTable::class, (object) ['theme' => 'bootstrap', 'field' => 'id']],
]);
