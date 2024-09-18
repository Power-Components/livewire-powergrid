<?php

use PowerComponents\LivewirePowerGrid\Tests\Concerns\Components\{DishesTable, DishesTableWithHideFooter};

use function PowerComponents\LivewirePowerGrid\Tests\Plugins\livewire;

it('show footer', function (string $component, string $theme) {
    livewire($component)
        ->call($theme)
        ->call('fetchDatasource')
        ->assertSee(['Showing', 'footer']);
})->with('show_footer_dataset')->group('action');

it('hide footer', function (string $component, string $theme) {
    livewire($component)
        ->call($theme)
        ->call('fetchDatasource')
        ->assertDontSee(['Showing', 'footer']);
})->with('hide_footer_dataset')->group('action');

dataset('show_footer_dataset', [
    'tailwind'  => [DishesTable::class, 'tailwind'],
    'bootstrap' => [DishesTable::class, 'bootstrap'],
]);

dataset('hide_footer_dataset', [
    'tailwind'  => [DishesTableWithHideFooter::class, 'tailwind'],
    'bootstrap' => [DishesTableWithHideFooter::class, 'bootstrap'],
]);
