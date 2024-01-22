<?php

use PowerComponents\LivewirePowerGrid\Tests\Concerns\Components\DishesTable;

use function PowerComponents\LivewirePowerGrid\Tests\Plugins\livewire;

it('deferLoading work properly', function (string $component, object $params) {
    livewire($component, [
        'join'         => $params->join,
        'deferLoading' => true,
    ])
        ->call($params->theme)
        ->call('fetchDatasource')
        ->set('setUp.footer.perPage', 11)
        ->assertSeeHtmlInOrder(['Showing', '1', 'to', '11', 'of', '15', 'Results']);
})->with('defer_loading_join')->group('action');

dataset('defer_loading_join', [
    'tailwind'          => [DishesTable::class, (object) ['theme' => 'tailwind', 'join' => false]],
    'bootstrap'         => [DishesTable::class, (object) ['theme' => 'bootstrap', 'join' => false]],
    'tailwind => join'  => [DishesTable::class, (object) ['theme' => 'tailwind', 'join' => true]],
    'bootstrap => join' => [DishesTable::class, (object) ['theme' => 'bootstrap', 'join' => true]],
]);
