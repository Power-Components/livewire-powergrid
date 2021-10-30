<?php

use Illuminate\Database\Eloquent\Collection;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\Tests\Models\{Category, Dish};

it('it infers a models attribute', function () {
    $collection = new Collection([
        new Dish([
            'id'   => '1',
            'name' => 'Bolo',
        ]),
    ]);

    $grid =  PowerGrid::eloquent($collection)
        ->addColumn('id')
        ->addColumn('name')
        ->make();

    expect($grid)->toHaveCount(1)
        ->and($grid[0])->toMatchObject(['id' => 1, 'name' => 'Bolo']);
});

it('returns relationships properly', function () {
    $stub = new Dish([
        'id'   => '1',
        'name' => 'Bolo',
    ]);

    $stub->setRelation('parent', new Category(['name' => 'Parent']));

    $collection = new Collection([$stub]);

    $grid =  PowerGrid::eloquent($collection)
        ->addColumn('id')
        ->addColumn('name')
        ->addColumn('my_arbitrary_name', fn ($model) => $model->parent->name)
        ->make();

    expect($grid)->toHaveCount(1)
        ->and($grid[0])->toMatchObject(['id' => 1, 'name' => 'Bolo', 'my_arbitrary_name' => 'Parent']);
});
