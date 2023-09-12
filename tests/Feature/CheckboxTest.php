<?php

use function PowerComponents\LivewirePowerGrid\Tests\Plugins\livewire;

;

use PowerComponents\LivewirePowerGrid\Tests\{DishesTable, DishesTableWithJoin};

it('selectCheckboxAll works properly', function (string $component, object $params) {
    $component = livewire($component)
        ->call($params->theme)
        ->set('checkboxAll', true)
        ->call('selectCheckboxAll');

    expect($component->checkboxValues)
        ->toMatchArray([
            0 => "1",
            1 => "2",
            2 => "3",
            3 => "4",
            4 => "5",
            5 => "6",
            6 => "7",
            7 => "8",
            8 => "9",
            9 => "10",
        ]);

    $component->call('setPage', 2)
        ->set('checkboxAll', true)
        ->call('selectCheckboxAll');

    expect($component->checkboxValues)
        ->toMatchArray([
            0  => "1",
            1  => "2",
            2  => "3",
            3  => "4",
            4  => "5",
            5  => "6",
            6  => "7",
            7  => "8",
            8  => "9",
            9  => "10",
            10 => "11",
            11 => "12",
            12 => "13",
            13 => "14",
            14 => "15",
        ]);

    $component->call('setPage', 1)
        ->set('checkboxAll', false)
        ->call('selectCheckboxAll');

    expect($component->checkboxValues)
        ->toBe([]);
})->with('checkbox_join');

dataset('checkbox_join', [
    'tailwind -> id'         => [DishesTable::class, (object) ['theme' => 'tailwind', 'field' => 'id']],
    'bootstrap -> id'        => [DishesTable::class, (object) ['theme' => 'bootstrap', 'field' => 'id']],
    'tailwind -> dishes.id'  => [DishesTableWithJoin::class, (object) ['theme' => 'tailwind', 'field' => 'dishes.id']],
    'bootstrap -> dishes.id' => [DishesTableWithJoin::class, (object) ['theme' => 'bootstrap', 'field' => 'dishes.id']],
]);
