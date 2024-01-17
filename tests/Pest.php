<?php

use Illuminate\Support\Facades\DB;
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Models\Dish;
use PowerComponents\LivewirePowerGrid\Tests\TestCase;
use PowerComponents\LivewirePowerGrid\{Column, PowerGridComponent};

uses(TestCase::class)->in(__DIR__);

function powergrid(): PowerGridComponent
{
    $columns = [
        Column::add()
            ->title('Id')
            ->field('id')
            ->searchable()
            ->sortable(),

        Column::add()
            ->title('Name')
            ->field('name')
            ->searchable()
            ->sortable(),
    ];

    $component             = new PowerGridComponent(1);
    $component->datasource = Dish::query();
    $component->columns    = $columns;

    return $component;
}

function requiresMySQL()
{
    if (DB::getDriverName() !== 'mysql') {
        test()->markTestSkipped('This test requires MySQL database');
    }

    return test();
}

function requiresSQLite()
{
    if (DB::getDriverName() !== 'sqlite') {
        test()->markTestSkipped('This test requires SQLite database');
    }

    return test();
}

function skipOnSQLite()
{
    if (DB::getDriverName() === 'sqlite') {
        test()->markTestSkipped('This test requires MYSQL/PGSQL database');
    }

    return test();
}

function requiresPostgreSQL()
{
    if (DB::getDriverName() !== 'pgsql') {
        test()->markTestSkipped('This test requires PostgreSQL database');
    }

    return test();
}

function requiresOpenSpout()
{
    $isInstalled = \Composer\InstalledVersions::isInstalled('openspout/openspout');

    if (!$isInstalled) {
        test()->markTestSkipped('This test requires openspout/openspout');
    }

    return test();
}
