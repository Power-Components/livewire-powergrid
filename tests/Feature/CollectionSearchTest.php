<?php

use PowerComponents\LivewirePowerGrid\Tests\Concerns\Components\{DishesCollectionTable};

use function PowerComponents\LivewirePowerGrid\Tests\Plugins\livewire;

use PowerComponents\LivewirePowerGrid\Themes\{Bootstrap5, Tailwind};

it('searches', function (string $component, object $params) {
    livewire($component)
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
    'tailwind'  => [DishesCollectionTable::class, (object) ['theme' => Tailwind::class]],
    'bootstrap' => [DishesCollectionTable::class, (object) ['theme' => Bootstrap5::class]],
]);
