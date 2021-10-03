<?php

use PowerComponents\LivewirePowerGrid\Tests\DishesTable;

it('properly displayes "full" showRecordCount')
    ->livewire(DishesTable::class)
    ->assertSeeTextInOrder(['Showing', '1','to', '10', 'of', '102', 'Results']);

it('properly displayes "short" showRecordCount')
    ->livewire(DishesTable::class)
    ->set('recordCount', 'short')
    ->assertSeeTextInOrder(['1', '-','10','|', '102']);

it('properly displayes "min" showRecordCount')
    ->livewire(DishesTable::class)
    ->set('recordCount', 'min')
    ->assertSeeTextInOrder(['1', '10']);

it('properly changes records per page')
    ->livewire(DishesTable::class)
    ->set('perPage', '25')
    ->assertSeeTextInOrder(['Showing', '1','to', '25', 'of','102', 'Results']);

it('properly paginates', function () {
    $component = powergrid();
    $component->perPage = 5;
    $pagination = $component->fillData();

    expect($pagination->items())->toHaveCount(5);
    expect($pagination->first()->name)->toBe('Pastel de Nata');
    expect($pagination->total())->toBe(102);
    expect($pagination->perPage())->toBe(5);
});
