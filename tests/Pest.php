<?php

use Illuminate\Database\Eloquent\Collection;
use PowerComponents\LivewirePowerGrid\Tests\ModelStub;
use PowerComponents\LivewirePowerGrid\Tests\ParentStub;
use PowerComponents\LivewirePowerGrid\Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

function testDatasource(): Collection
{
    $johnSmith = new ModelStub([
        'id'   => '53',
        'name' => 'john smith'
    ]);
    $johnSmith->setRelation('parent', new ParentStub(['name' => 'John Smith relation']));

    $anna = new ModelStub([
        'id'   => '57',
        'name' => 'anna'
    ]);
    $anna->setRelation('parent', new ParentStub(['name' => 'Anna relation']));

    $john = new ModelStub([
        'id'   => '58',
        'name' => 'john'
    ]);
    $john->setRelation('parent', new ParentStub(['name' => 'John relation']));

    $thales = new ModelStub([
        'id'   => '150',
        'name' => 'Thales'
    ]);
    $thales->setRelation('parent', new ParentStub(['name' => 'Thales relation']));

    return new Collection([
        $johnSmith,
        $anna,
        $john,
        $thales
    ]);
}
