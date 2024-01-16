<?php

use PowerComponents\LivewirePowerGrid\Tests\Concerns\Components\DishesSearchJSONTable;

use function PowerComponents\LivewirePowerGrid\Tests\Plugins\livewire;

it('searches JSON column', function (string $component, object $params) {
    livewire($component)
        ->call($params->theme)
        ->set('search', 'uramaki')
        ->assertSee('Barco-Sushi Simples')
        ->assertDontSee('Barco-Sushi da Sueli')

        ->set('search', 'URAMAKI')
        ->assertSee('Barco-Sushi Simples')
        ->assertDontSee('Barco-Sushi da Sueli')

        ->set('search', 'UraMaKi')
        ->assertSee('Barco-Sushi Simples')
        ->assertDontSee('Barco-Sushi da Sueli');
})->with('search-json');

dataset('search-json', [
    'tailwind'  => [DishesSearchJSONTable::class, (object) ['theme' => 'tailwind']],
    'bootstrap' => [DishesSearchJSONTable::class, (object) ['theme' => 'bootstrap']],
]);
