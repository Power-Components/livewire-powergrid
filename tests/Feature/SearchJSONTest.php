<?php

use PowerComponents\LivewirePowerGrid\Tests\Concerns\Components\DishesSearchJSONTable;

use function PowerComponents\LivewirePowerGrid\Tests\Plugins\livewire;

it('searches JSON column', function (string $component, object $params) {
    livewire($component)
        ->call('setTestThemeClass', $params->theme)
        ->set('search', 'uramaki')
        ->assertSee('Barco-Sushi Simples')
        ->assertDontSee('Barco-Sushi da Sueli')

        ->set('search', 'URAMAKI')
        ->assertSee('Barco-Sushi Simples')
        ->assertDontSee('Barco-Sushi da Sueli')

        ->set('search', 'UraMaKi')
        ->assertSee('Barco-Sushi Simples')
        ->assertDontSee('Barco-Sushi da Sueli');
})->with('search-json')->skipOnPostgreSQL();

dataset('search-json', [
    'tailwind'  => [DishesSearchJSONTable::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\Tailwind::class]],
    'bootstrap' => [DishesSearchJSONTable::class, (object) ['theme' => \PowerComponents\LivewirePowerGrid\Themes\Bootstrap5::class]],
]);
