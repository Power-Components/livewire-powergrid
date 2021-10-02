<?php

use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\Tests\ModelStub;

beforeEach(function () {
    $this->columns         = [
        Column::add()
            ->title(__('ID'))
            ->field('id')
            ->searchable()
            ->sortable(),

        Column::add()
            ->title(__('Name'))
            ->field('name')
            ->searchable()
            ->editOnClick(true)
            ->clickToCopy(true)
            ->makeInputText('name')
            ->sortable(),
        ];

    $component = new PowerGridComponent(1);
    $component->datasource = ModelStub::query();
    $component->columns    = $this->columns;
    $component->perPage    = 10;

    $this->component       = $component;
});

it('properly filters by "name is"', function () {
    $this->component->filters = [
        "input_text" => [
            "name" => "Peixada da chef Nábia - 1"
        ],
        "input_text_options" => [
            "name" => "is"
        ]
    ];

    $pagination = $this->component->fillData();

    expect($pagination)
        ->toHaveCount(1);
    expect($pagination->first()->name)
        ->toBe('Peixada da chef Nábia - 1');
});

it('properly filters by "name is" when name is not present', function () {
    $this->component->filters = [
        "input_text" => [
            "name" => "Peixada do João"
        ],
        "input_text_options" => [
            "name" => "is"
        ]
    ];

    $pagination = $this->component->fillData();

    expect($pagination->total())
        ->toBe(0);
});

it('properly filters by "name is not"', function () {
    $this->component->filters = [
        "input_text" => [
            "name" => "Peixada da chef Nábia - 1"
        ],
        "input_text_options" => [
            "name" => "is_not"
        ]
    ];

    $pagination = $this->component->fillData();

    expect($pagination->total())->toBe(305);
});

it('properly filters by "name is not" when name is not present', function () {
    $this->component->filters = [
        "input_text" => [
            "name" => "Peixada do João"
        ],
        "input_text_options" => [
            "name" => "is_not"
        ]
    ];

    $pagination = $this->component->fillData();

    expect($pagination->total())->toBe(306);
});

it('properly filters by "name contains"', function () {
    $this->component->filters = [
        "input_text" => [
            "name" => "Tomates"
        ],
        "input_text_options" => [
            "name" => "contains"
        ]
    ];

    $pagination = $this->component->fillData();

    expect($pagination->items())->toHaveCount(3);

    foreach ($pagination->items() as $item) {
        expect($item->name)->toContain('Sopa de Tomates Assados');
    }
});

it('properly filters by "name starts_with"', function () {
    $this->component->filters = [
        "input_text" => [
            "name" => "Sopa"
        ],
        "input_text_options" => [
            "name" => "starts_with"
        ]
    ];

    $pagination = $this->component->fillData();

    expect($pagination->items())->toHaveCount(6);

    /** @var ModelStub $item */
    foreach ($pagination->items() as $item) {
        expect($item->name)->toContain('Sopa');
    }
});

