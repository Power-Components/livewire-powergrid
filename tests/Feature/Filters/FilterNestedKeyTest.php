<?php

use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Models\Dish;
use PowerComponents\Turbine\DataSource\Builders\Select;

uses()->group('filters', 'security', 'database');

function nestedKeyComponent(): PowerGridComponent
{
    return new class() extends PowerGridComponent
    {
        public string $tableName = 'nested-key';
    };
}

it('appends a plain identifier key onto the column', function () {
    $query = Dish::query();

    (new Select(nestedKeyComponent()))->builder($query, 'name', ['en' => 'Pastel']);

    expect($query->toSql())->toContain('en')
        ->and($query->getBindings())->toContain('Pastel');
});

it('ignores an unexpected key and keeps the base column', function () {
    $query = Dish::query();

    (new Select(nestedKeyComponent()))->builder($query, 'name', ['bad key' => 'Pastel']);

    expect($query->toSql())->not->toContain('bad')
        ->and($query->getBindings())->toContain('Pastel');
});
