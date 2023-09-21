<?php

use PowerComponents\LivewirePowerGrid\Tests\DishesBeforeSearchTable;

;

use function PowerComponents\LivewirePowerGrid\Tests\Plugins\livewire;

it('searches data using beforeSearch', function (string $component, object $params) {
    livewire($component)
        ->call($params->theme)
        ->set('search', 'Pastel')
        ->assertSee('Peixada')
        ->set('search', 'Francesinha')
        ->assertSee('Peixada')
        ->set('search', '')
        ->assertSee('Peixada');
})->with('before_search_themes');

dataset('before_search_themes', [
    'tailwind'  => [DishesBeforeSearchTable::class, (object) ['theme' => 'tailwind']],
    'bootstrap' => [DishesBeforeSearchTable::class, (object) ['theme' => 'bootstrap']],
]);
