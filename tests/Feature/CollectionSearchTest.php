<?php

use PowerComponents\LivewirePowerGrid\Tests\Concerns\Components\DishesIterableTable;
use PowerComponents\LivewirePowerGrid\Themes\Tailwind;

use function PowerComponents\LivewirePowerGrid\Tests\Plugins\livewire;

it('searches', function (string $component, object $params) {
    livewire($component, [
        'iterableType' => 'collection',
    ])
        ->call('setTestThemeClass', $params->theme)
        ->set('search', 'Name 1')
        ->assertSee('Name 1')
        ->assertDontSee('Name 2')
        ->assertDontSee('Name 3')
        ->assertDontSee('Name 4')
        ->assertDontSee('Name 5')

        ->set('search', 'Name 3')
        ->assertDontSee('Name 1')
        ->assertDontSee('Name 2')
        ->assertDontSee('Name 4')
        ->assertDontSee('Name 5')

        ->set('search', 'Name 5')
        ->assertDontSee('Name 1')
        ->assertDontSee('Name 2')
        ->assertDontSee('Name 3')
        ->assertDontSee('Name 4');
})->with('search');

dataset('search', [
    'tailwind' => [DishesIterableTable::class, (object) ['theme' => Tailwind::class]],
]);
