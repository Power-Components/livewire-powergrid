<?php

use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\Tests\DishesTable;
use PowerComponents\LivewirePowerGrid\Tests\Models\Dish;

use function Pest\Livewire\livewire;

it('properly paginates data', function () {
    $component = new PowerGridComponent(1);
    $component->datasource = Dish::query();
    $component->perPage    = 10;

    $pagination = $component->fillData();

    expect($pagination->total())->toBe(102);
    expect($pagination->perPage())->toBe(10);
});

it('showRecordCount full record count work', function () {
    $component = livewire(DishesTable::class);
    $component->assertSeeTextInOrder(['Showing', '1','to', '10', 'of', '102', 'Results']);
});

it('showRecordCount short record count word', function () {
    $component = livewire(DishesTable::class);
    $component->set('recordCount', 'short');
    $component->assertSeeTextInOrder(['1', '-','10','|', '102']);
});

it('showRecordCount min record count word', function () {
    $component = livewire(DishesTable::class);
    $component->set('recordCount', 'min');
    $component->assertSeeTextInOrder(['1', '10']);
});


it('property perPage work when changed', function () {
    $component = livewire(DishesTable::class);
    $component->set('perPage', '25');

    $component->assertSeeTextInOrder(['Showing', '1','to', '25', 'of','102', 'Results']);
});




