<?php

namespace PowerComponents\LivewirePowerGrid\Tests\Feature;

use Illuminate\Database\Eloquent\Collection;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\Tests\Models\Category;
use PowerComponents\LivewirePowerGrid\Tests\Models\Dish;
use PowerComponents\LivewirePowerGrid\Tests\TestCase;

class PowerGridTest extends TestCase
{
    /** @test */
    public function it_infers_a_models_attribute()
    {
        $collection = new Collection([
            new Dish([
                'id'   => '1',
                'name' => 'Mateus'
            ])
        ]);

        $grid =  PowerGrid::eloquent($collection)
            ->addColumn('id')
            ->addColumn('name');

        $this->assertEquals([
            (object)[
                'id'   => 1,
                'name' => 'Mateus'
            ]
        ], $grid->make());
    }

    /** @test */
    public function it_returns_relationships_properly()
    {
        $stub = new Dish([
            'id'   => '1',
            'name' => 'Mateus'
        ]);

        $stub->setRelation('parent', new Category(['name' => 'Parent']));

        $collection = new Collection([$stub]);

        $grid =  PowerGrid::eloquent($collection)
            ->addColumn('id')
            ->addColumn('name')
            ->addColumn('my_arbitrary_name', fn ($model) => $model->parent->name);

        $this->assertEquals([
            (object)[
                'id'                => 1,
                'name'              => 'Mateus',
                'my_arbitrary_name' => 'Parent'
            ]
        ], $grid->make());
    }
}
