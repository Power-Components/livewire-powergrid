<?php

namespace PowerComponents\LivewirePowerGrid\Tests\Feature;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\Tests\TestCase;

class PowerGridTest extends TestCase
{
    /** @test */
    public function it_infers_a_models_attribute()
    {
        $collection = new Collection([
            new ModelStub([
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
        $stub = new ModelStub([
            'id'   => '1',
            'name' => 'Mateus'
        ]);

        $stub->setRelation('parent', new ParentStub(['name' => 'Parent']));

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
