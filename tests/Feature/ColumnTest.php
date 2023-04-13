<?php

use function Pest\Livewire\livewire;

use PowerComponents\LivewirePowerGrid\Tests\{DishesTable, DishesTableWithJoin};

it('sorts by "name" and then by "id"', function (string $component, object $params) {
    livewire($component)
        ->call($params->theme)
        ->assertSeeHtml('Pastel de Nata')
        ->call('sortBy', $params->field)
        ->assertSeeHtml('Almôndegas ao Sugo')
        ->assertDontSeeHtml('Pastel de Nata')
        ->call('sortBy', 'id')
        ->assertSeeHtml('Pastel de Nata');
})->with('column_join');

it('searches data', function (string $component, object $params) {
    livewire($component)
        ->call($params->theme)
        ->assertSeeHtml('Pastel de Nata')
        ->set('search', 'Sugo')
        ->assertSeeHtml('Almôndegas ao Sugo')
        ->assertDontSeeHtml('Pastel de Nata');
})->with('themes')->skip();

it('searches data as case insensitive', function (string $component, object $params) {
    livewire($component)
        ->call($params->theme)
        ->assertSeeHtml('Pastel de Nata')
        ->set('search', 'sugo')
        ->assertSeeHtml('Almôndegas ao Sugo')
        ->assertDontSeeHtml('Pastel de Nata');
})->with('themes')->skip();

dataset('column_join', [
    'tailwind'       => [DishesTable::class, (object) ['theme' => 'tailwind', 'field' => 'name']],
    'bootstrap'      => [DishesTable::class, (object) ['theme' => 'bootstrap', 'field' => 'name']],
    'tailwind join'  => [DishesTableWithJoin::class, (object) ['theme' => 'tailwind', 'field' => 'dishes.name']],
    'bootstrap join' => [DishesTableWithJoin::class, (object) ['theme' => 'bootstrap', 'field' => 'dishes.name']],
]);
