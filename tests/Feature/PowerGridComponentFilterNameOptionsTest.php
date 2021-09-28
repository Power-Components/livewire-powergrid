<?php

use Illuminate\Database\Eloquent\Collection;

use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\Tests\Feature\ModelStub;

beforeEach(function () {
    $this->component = new PowerGridComponent;
    $this->columns = [
        'id',
        'name'
    ];

    $this->data = new Collection([
        new ModelStub([
            'id'   => '1',
            'name' => 'john smith'
        ]),
        new ModelStub([
            'id'   => '2',
            'name' => 'john'
        ]),
        new ModelStub([
            'id'   => '3',
            'name' => 'anna'
        ]),
        new ModelStub([
            'id'   => '4',
            'name' => 'thales'
        ])
    ]);
});

it('clean filters', function () {
    $this->component->filters = [
        "input_text" => [
            "name" => "pizza"
        ]
    ];
    $this->assertNotEmpty($this->component->filters);

    $this->component->clearFilter();

    $this->assertEquals($this->component->filters, []);
});

it('properly filters by "name is"', function () {

    $this->component->filters = [
        "input_text" => [
            "name" => "john"
        ],
        "input_text_options" => [
            "name" => "is"
        ]
    ];

    $filtered = \PowerComponents\LivewirePowerGrid\Helpers\Collection::query($this->data)
        ->setColumns($this->columns)
        ->setSearch('')
        ->setFilters($this->component->filters)
        ->filterContains()
        ->filter();

    $this->assertCount(1,$filtered);
    $this->AssertEquals($filtered->first()->name, 'john');
});

it('properly filters by "name is" when name is not present', function () {

    $this->component->filters = [
        "input_text" => [
            "name" => "maria"
        ],
        'input_text_options' => [
            'name' => 'is'
        ]
    ];

    $filtered = \PowerComponents\LivewirePowerGrid\Helpers\Collection::query($this->data)
        ->setColumns($this->columns)
        ->setSearch('')
        ->setFilters($this->component->filters)
        ->filterContains()
        ->filter();
    $this->assertEmpty($filtered);
});

it('properly filters by "name is not"', function () {

    $this->component->filters = [
        'input_text' => [
            'name' => 'john'
        ],
        'input_text_options' => [
            'name' => 'is_not'
        ]
    ];

    $filtered = \PowerComponents\LivewirePowerGrid\Helpers\Collection::query($this->data)
        ->setColumns($this->columns)
        ->setSearch('')
        ->setFilters($this->component->filters)
        ->filterContains()
        ->filter();

    $this->assertCount(3, $filtered);
});

it('properly filters by "name is not" when name is not present', function () {

    $this->component->filters = [
        'input_text' => [
            'name' => 'anna'
        ],
        'input_text_options' => [
            'name' => 'is_not'
        ]
    ];

    $filtered = \PowerComponents\LivewirePowerGrid\Helpers\Collection::query($this->data)
        ->setColumns($this->columns)
        ->setSearch('')
        ->setFilters($this->component->filters)
        ->filterContains()
        ->filter();

    $this->assertCount(3, $filtered);
});

it('properly filters by "name contains"', function () {

    $this->component->filters = [
        "input_text" => [
            "name" => "th"
        ],
        "input_text_options" => [
            "name" => "contains"
        ]
    ];

    $filtered = \PowerComponents\LivewirePowerGrid\Helpers\Collection::query($this->data)
        ->setColumns($this->columns)
        ->setSearch('')
        ->setFilters($this->component->filters)
        ->filterContains()
        ->filter();

    $this->assertCount(2,$filtered);
});

it('properly filters by "name starts_with"', function () {

    $this->component->filters = [
        "input_text" => [
            "name" => "john"
        ],
        "input_text_options" => [
            "name" => "starts_with"
        ]
    ];

    $filtered = \PowerComponents\LivewirePowerGrid\Helpers\Collection::query($this->data)
        ->setColumns($this->columns)
        ->setSearch('')
        ->setFilters($this->component->filters)
        ->filterContains()
        ->filter();

    $this->assertCount(2,$filtered);
});

it('properly filters by "name ends_with"', function () {

    $this->component->filters = [
        "input_text" => [
            "name" => "smith"
        ],
        "input_text_options" => [
            "name" => "ends_with"
        ]
    ];

    $filtered = \PowerComponents\LivewirePowerGrid\Helpers\Collection::query($this->data)
        ->setColumns($this->columns)
        ->setSearch('')
        ->setFilters($this->component->filters)
        ->filterContains()
        ->filter();

    $this->assertCount(1,$filtered);
});
