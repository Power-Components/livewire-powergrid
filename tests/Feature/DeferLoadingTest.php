<?php

use function Pest\Livewire\livewire;

use PowerComponents\LivewirePowerGrid\Tests\DishesTable;

it('deferLoading work properly', function (string $component, object $params) {
    livewire($component, [
        'join'         => $params->join,
        'deferLoading' => true,
    ])
        ->call($params->theme)
        ->call('fetchDatasource')
        ->set('setUp.footer.perPage', 25)
        ->assertSeeTextInOrder(['Showing', '1', 'to', '25', 'of', '102', 'Results']);
})->with('defer')->group('action');

dataset('defer', [
    'tailwind'          => [DishesTable::class, (object) ['theme' => 'tailwind', 'join' => false]],
    'bootstrap'         => [DishesTable::class, (object) ['theme' => 'bootstrap', 'join' => false]],
    'tailwind => join'  => [DishesTable::class, (object) ['theme' => 'tailwind', 'join' => true]],
    'bootstrap => join' => [DishesTable::class, (object) ['theme' => 'bootstrap', 'join' => true]],
]);
