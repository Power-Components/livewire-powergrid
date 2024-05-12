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
        test()->skipWithReason('This test requires MySQL database');
    }

    return test();
}
function skipOnMySQL(string $reason = '')
{
    if (DB::getDriverName() === 'mysql') {
        test()->skipWithReason('Skipping on MySQL', $reason);
    }

    return test();
}

function requiresSQLite()
{
    if (DB::getDriverName() !== 'sqlite') {
        test()->skipWithReason('This test requires SQLite database');
    }

    return test();
}

function skipOnSQLite(string $reason = '')
{
    if (DB::getDriverName() === 'sqlite') {
        test()->skipWithReason('Skipping on SQLite', $reason);
    }

    return test();
}

function requiresPostgreSQL()
{
    if (DB::getDriverName() !== 'pgsql') {
        test()->skipWithReason('This test requires PostgreSQL database');
    }

    return test();
}

function skipOnPostgreSQL(string $reason = '')
{
    if (DB::getDriverName() === 'pgsql') {
        test()->skipWithReason('Skipping on PostgreSQL', $reason);
    }

    return test();
}

function requiresOpenSpout()
{
    $isInstalled = \Composer\InstalledVersions::isInstalled('openspout/openspout');

    if (!$isInstalled) {
        test()->skipWithReason('test requires openspout/openspout');
    }

    return test();
}

function fixturePath(string $filepath): string
{
    return str_replace('/', DIRECTORY_SEPARATOR, __DIR__ . '/Concerns/Fixtures/' . ltrim($filepath, '/'));
}

function skipWithReason(string $default, string $reason = ''): void
{
    $reason = str($reason)->whenNotEmpty(fn ($r) => $r->prepend(': '))
            ->prepend($default)
            ->toString();

    test()->markTestSkipped($reason);
}
