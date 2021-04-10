<?php

namespace PowerComponents\LivewirePowerGrid\Tests;

use PHPUnit\Framework\TestCase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use PowerComponents\LivewirePowerGrid\PowerGrid;

class PowerGridTest extends TestCase
{
    /** @test */
    public function it_infers_a_models_attribute()
    {
        $collection = new Collection([
            new ModelStub([
                'id' => '1',
                'name' => 'Mateus'
            ])
        ]);

        $grid =  PowerGrid::eloquent($collection)
                        ->addColumn('id')
                        ->addColumn('name');

        $this->assertEquals([
            (object) [
                'id' => 1,
                'name' => 'Mateus'
            ]
            ], $grid->make());
    }
}

class ModelStub extends Model
{
    protected $guarded = [];
}
