<?php

use function Pest\Livewire\livewire;

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
            15 => "16",
            16 => "17",
            17 => "18",
            18 => "19",
            19 => "20",
        ]);

    $component->call('setPage', 1)
        ->set('checkboxAll', false)
        ->call('selectCheckboxAll');

    expect($component->checkboxValues)
        ->toBe([]);
})->with('themes');
