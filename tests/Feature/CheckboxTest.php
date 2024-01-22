<?php

use PowerComponents\LivewirePowerGrid\Facades\Rule;
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Components\DishTableBase;

use function PowerComponents\LivewirePowerGrid\Tests\Plugins\livewire;

$componentWithActionRules = new class () extends DishTableBase {
    public function actionRules($row): array
    {
        $idsWithCheckboxDisabled = [1, 2, 3];

        return [
            Rule::checkbox()
                ->when(fn ($dish) => in_array($dish->id, $idsWithCheckboxDisabled))
                ->disable(),
        ];
    }
};

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
})->with([
    'tailwind -> id'  => [DishTableBase::class, (object) ['theme' => 'tailwind', 'field' => 'id']],
    'bootstrap -> id' => [DishTableBase::class, (object) ['theme' => 'bootstrap', 'field' => 'id']],
]);

it('selectCheckboxAll works properly with actionRules disable', function (string $component, object $params) {
    $component = livewire($component)
        ->call($params->theme)
        ->set('checkboxAll', true)
        ->call('selectCheckboxAll');

    expect($component->checkboxValues)
        ->toMatchArray([
            0 => "4",
            1 => "5",
            2 => "6",
            3 => "7",
            4 => "8",
            5 => "9",
            6 => "10",
        ]);

    $component->call('setPage', 2)
        ->set('checkboxAll', true)
        ->call('selectCheckboxAll');

    expect($component->checkboxValues)
        ->toMatchArray([
            0  => "4",
            1  => "5",
            2  => "6",
            3  => "7",
            4  => "8",
            5  => "9",
            6  => "10",
            7  => "11",
            8  => "12",
            9  => "13",
            10 => "14",
            11 => "15",
        ]);

    $component->call('setPage', 1)
        ->set('checkboxAll', false)
        ->call('selectCheckboxAll');

    expect($component->checkboxValues)
        ->toBe([]);
})->with([
    'tailwind -> id'  => [$componentWithActionRules::class, (object) ['theme' => 'tailwind', 'field' => 'id']],
    'bootstrap -> id' => [$componentWithActionRules::class, (object) ['theme' => 'bootstrap', 'field' => 'id']],
]);
