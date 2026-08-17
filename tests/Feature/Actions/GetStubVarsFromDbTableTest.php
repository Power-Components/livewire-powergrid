<?php

use Illuminate\Support\Facades\Schema;
use PowerComponents\LivewirePowerGrid\Commands\Actions\GetStubVarsFromDbTable;
use PowerComponents\LivewirePowerGrid\Commands\Enums\Datasource;
use PowerComponents\LivewirePowerGrid\Commands\Support\PowerGridComponentMaker;

beforeEach(fn () => createWaitersTable());

afterEach(fn () => Schema::dropIfExists('waiters'));

function waiterQueryBuilderComponent(): PowerGridComponentMaker
{
    return PowerGridComponentMaker::make('WaiterTable')
        ->setDatasource(Datasource::QUERY_BUILDER)
        ->setDatabaseTable('waiters')
        ->setAutoCreateColumns();
}

it('reads every column of the given table', function () {
    $vars = GetStubVarsFromDbTable::handle(waiterQueryBuilderComponent());

    expect($vars['PowerGridFields'])
        ->toContain("->add('id')")
        ->toContain("->add('name')")
        ->toContain("->add('email')")
        ->toContain("->add('tips')")
        ->toContain("->add('hired_at_formatted'")
        ->toContain("->add('created_at_formatted'");
});

it('drops sensitive columns', function () {
    $vars = GetStubVarsFromDbTable::handle(waiterQueryBuilderComponent());

    expect($vars)->each->not->toContain('password')
        ->and($vars)->each->not->toContain('remember_token');
});

it('has no model to hide columns with, unlike the Eloquent source', function () {
    $vars = GetStubVarsFromDbTable::handle(waiterQueryBuilderComponent());

    expect($vars['PowerGridFields'])->toContain("->add('internal_note')");
});

it('leaves the generated closures untyped', function () {
    $vars = GetStubVarsFromDbTable::handle(waiterQueryBuilderComponent());

    expect($vars['PowerGridFields'])->toContain('fn ($model)');
});

it('generates nothing when the table does not exist', function () {
    $component = PowerGridComponentMaker::make('GhostTable')
        ->setDatasource(Datasource::QUERY_BUILDER)
        ->setDatabaseTable('table_that_does_not_exist')
        ->setAutoCreateColumns();

    expect(GetStubVarsFromDbTable::handle($component)['PowerGridFields'])->toBe('');
});

it('renders the generated columns into the query builder stub', function () {
    $component = waiterQueryBuilderComponent()->loadPowerGridStub();

    $code = $component->saveToString();

    expect($code)
        ->toContain("return DB::table('waiters');")
        ->toContain("->add('tips')")
        ->toContain("Column::make('Email', 'email')")
        ->toContain("Filter::inputText('email')->operators(['contains'])")
        ->toContain("Column::action('Action')")
        ->not->toContain('{{ columns }}')
        ->not->toContain('password');
});
