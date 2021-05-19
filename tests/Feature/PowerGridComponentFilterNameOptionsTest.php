<?php

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

beforeEach(function () {
    $this->component = new PowerGridComponent;
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

/*
        $pg = new PowerGridComponent();
        $filters = $pg->filters;
        $model = Dish::query();

*/

    $data = new Collection([
        new ModelStub([
            'id'   => '1',
            'name' => 'john smith'
        ]),
        new ModelStub([
            'id'   => '2',
            'name' => 'john'
        ])
    ]);

    $this->component->filters = [
        "input_text" => [
            "name" => "john"
        ],
        'input_text_options' => [
            'name' => 'is'
        ]
    ];

    $filtered = \PowerComponents\LivewirePowerGrid\Helpers\Collection::filter($this->component->filters, $data);

    $this->assertCount(1,$filtered);
    $this->AssertEquals($filtered->first()->name, 'john');
});

it('properly filters by "name is" when name is not present', function () {
    $data = new Collection([
        new ModelStub([
            'id'   => '1',
            'name' => 'anna'
        ]),
        new ModelStub([
            'id'   => '2',
            'name' => 'maria'
        ])
    ]);

    $this->component->filters = [
        "input_text" => [
            "name" => "john"
        ],
        'input_text_options' => [
            'name' => 'is'
        ]
    ];

    $filtered = \PowerComponents\LivewirePowerGrid\Helpers\Collection::filter($this->component->filters, $data);
    $this->assertEmpty($filtered);
});

it('properly filters by "name is not"', function () {
    $data = new Collection([
        new ModelStub([
            'id'   => '1',
            'name' => 'john'
        ]),
        new ModelStub([
            'id'   => '2',
            'name' => 'john smith'
        ])
    ]);

    $this->component->filters = [
        'input_text' => [
            'name' => 'john'
        ],
        'input_text_options' => [
            'name' => 'is_not'
        ]
    ];

    $filtered = \PowerComponents\LivewirePowerGrid\Helpers\Collection::filter($this->component->filters, $data);

    $this->assertCount(1, $filtered);
    $this->AssertEquals($filtered->first()->name, 'john smith');
});

it('properly filters by "name is not" when name is not present', function () {
    $data = new Collection([
        new ModelStub([
            'id'   => '1',
            'name' => 'john'
        ]),
        new ModelStub([
            'id'   => '2',
            'name' => 'john smith'
        ])
    ]);

    $this->component->filters = [
        'input_text' => [
            'name' => 'anna'
        ],
        'input_text_options' => [
            'name' => 'is_not'
        ]
    ];

    $filtered = \PowerComponents\LivewirePowerGrid\Helpers\Collection::filter($this->component->filters, $data);

    $this->assertCount(2, $filtered);
});

class ModelStub extends Model
{
    protected $guarded = [];

    public function parent()
    {
        return $this->belongsTo(ParentStub::class);
    }
}

class ParentStub extends Model
{
    protected $guarded = [];
}
