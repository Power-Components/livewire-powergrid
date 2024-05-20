<?php

use PowerComponents\LivewirePowerGrid\Tests\Concerns\Components\DishesCalculationsCollectionTable;

use function PowerComponents\LivewirePowerGrid\Tests\Plugins\livewire;

it('calculates "count" on id field', function (string $component, object $params) {
    livewire($component)
        ->call($params->theme)
        ->assertSeeHtml('Count ID: 4 item(s)')
        ->set('search', 'Luan')
        ->assertSeeHtml('Count ID: 1 item(s)');
})->with('calculations collection');

it('calculates "sum" on price balance', function (string $component, object $params) {
    livewire($component)
        ->call($params->theme)
        ->assertSeeHtml('<span>Sum Balance: $671.66</span>')
        ->set('search', 'Luan')
        ->assertSeeHtml('<span>Sum Balance: $241.86</span>');
})->with('calculations collection');

it('calculates and formats "avg" on balance field and calorie fields', function (string $component, object $params) {
    livewire($component)
        ->call($params->theme)
        ->assertSeeHtml('<span>Avg Balance: $167.92</span>')
        ->set('search', 'Luan')
        ->assertSeeHtml('<span>Avg Balance: $241.86</span>');
})->with('calculations collection');

it('calculates "min" on balance field', function (string $component, object $params) {
    livewire($component)
        ->call($params->theme)
        ->assertSeeHtml('<span>Min Balance: $44.28</span>')
        ->set('search', 'Luan')
        ->assertSeeHtml('<span>Min Balance: $241.86</span>');
})->with('calculations collection');

it('calculates "max" on balance field', function (string $component, object $params) {
    livewire($component)
        ->call($params->theme)
        ->assertSeeHtml('<span>Max Balance: $241.86</span>')
        ->set('search', 'Luan')
        ->assertSeeHtml('<span>Max Balance: $241.86</span>');
})->with('calculations collection');

dataset('calculations collection', [
    'tailwind'  => [DishesCalculationsCollectionTable::class, (object) ['theme' => 'tailwind']],
    'bootstrap' => [DishesCalculationsCollectionTable::class, (object) ['theme' => 'bootstrap']],
]);
