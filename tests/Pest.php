<?php

use Illuminate\Database\Eloquent\Collection;
use PowerComponents\LivewirePowerGrid\Tests\ModelStub;
use PowerComponents\LivewirePowerGrid\Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

function testDataSource() {
    return new Collection([
        new ModelStub([
            'id'   => '53',
            'name' => 'john smith'
        ]),
        new ModelStub([
            'id'   => '55',
            'name' => 'john'
        ]),
        new ModelStub([
            'id'   => '57',
            'name' => 'anna'
        ]),
        new ModelStub([
            'id'   => '150',
            'name' => 'thales'
        ])
    ]);
}
