<?php

use Illuminate\Support\Facades\Schema;
use PowerComponents\LivewirePowerGrid\Commands\Actions\GetStubVarsFromFromModel;
use PowerComponents\LivewirePowerGrid\Commands\Enums\{ColumnSource, Datasource};
use PowerComponents\LivewirePowerGrid\Commands\Support\PowerGridComponentMaker;
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Fixtures\Models\{Waiter, WaiterWithKeyInFillable};

beforeEach(fn () => createWaitersTable());

afterEach(fn () => Schema::dropIfExists('waiters'));

function waiterComponent(ColumnSource $columnSource, string $modelFqn = Waiter::class): PowerGridComponentMaker
{
    return PowerGridComponentMaker::make('WaiterTable')
        ->setDatasource(Datasource::ELOQUENT_BUILDER)
        ->setModelWithFqn('Waiter', $modelFqn)
        ->setAutoCreateColumns()
        ->setColumnSource($columnSource);
}

it('reads the primary key, $fillable and created_at when the source is the model', function () {
    $vars = GetStubVarsFromFromModel::handle(waiterComponent(ColumnSource::FILLABLE));

    expect($vars['Fields'])
        ->toContain("->add('id')")
        ->toContain("->add('name')")
        ->toContain("->add('email')")
        ->toContain("->add('created_at_formatted'");
});

it('ignores columns outside $fillable when the source is the model', function () {
    $vars = GetStubVarsFromFromModel::handle(waiterComponent(ColumnSource::FILLABLE));

    expect($vars['Fields'])
        ->not->toContain('tips')
        ->not->toContain('hired_at')
        ->not->toContain('updated_at');
});

it('reads every table column when the source is the database table', function () {
    $vars = GetStubVarsFromFromModel::handle(waiterComponent(ColumnSource::DATABASE_TABLE));

    expect($vars['Fields'])
        ->toContain("->add('id')")
        ->toContain("->add('name')")
        ->toContain("->add('email')")
        ->toContain("->add('tips')")
        ->toContain("->add('hired_at_formatted'")
        ->toContain("->add('updated_at_formatted'");
});

it('types the columns it read from the database table', function () {
    $vars = GetStubVarsFromFromModel::handle(waiterComponent(ColumnSource::DATABASE_TABLE));

    expect($vars['columns'])
        ->toContain("Column::make('Tips', 'tips'),")
        ->toContain("Column::make('Hired at', 'hired_at_formatted', 'hired_at')")
        ->and($vars['filters'])
        ->toContain("Filter::datepicker('hired_at'),")
        ->toContain("Filter::inputText('name')->operators(['contains']),");
});

it('never generates a sensitive column from the database table', function (string $source) {
    $vars = GetStubVarsFromFromModel::handle(waiterComponent(ColumnSource::from($source)));

    expect($vars)->each->not->toContain('password')
        ->and($vars)->each->not->toContain('remember_token');
})->with(['FILLABLE', 'DATABASE_TABLE']);

it('never generates a $hidden column', function (string $source) {
    $vars = GetStubVarsFromFromModel::handle(waiterComponent(ColumnSource::from($source)));

    expect($vars)->each->not->toContain('internal_note');
})->with(['FILLABLE', 'DATABASE_TABLE']);

it('does not repeat a field listed in both $fillable and the historical order', function () {
    $vars = GetStubVarsFromFromModel::handle(
        waiterComponent(ColumnSource::FILLABLE, WaiterWithKeyInFillable::class)
    );

    expect(substr_count($vars['Fields'], "->add('id')"))->toBe(1)
        ->and(substr_count($vars['Fields'], "->add('created_at_formatted'"))->toBe(1);
});

it('type-hints the model in the generated closures', function () {
    $vars = GetStubVarsFromFromModel::handle(waiterComponent(ColumnSource::DATABASE_TABLE));

    expect($vars['Fields'])->toContain('fn (Waiter $model)')
        ->not->toContain('fn ($model)');
});

it('generates nothing when the table has not been migrated yet', function () {
    Schema::dropIfExists('waiters');

    $vars = GetStubVarsFromFromModel::handle(waiterComponent(ColumnSource::DATABASE_TABLE));

    expect($vars['Fields'])->toBe('')
        ->and($vars['filters'])->toBe("[\n        ];")
        ->and($vars['columns'])->toBe("[\n            Column::action('Action')\n        ];");
});
