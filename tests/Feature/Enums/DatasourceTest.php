<?php

use PowerComponents\LivewirePowerGrid\Enums\Datasource;

test('cases', function () {
    expect(Datasource::cases())->toBe([
        Datasource::ELOQUENT_BUILDER,
        Datasource::QUERY_BUILDER,
        Datasource::COLLECTION,
    ]);
});

it('has options', function () {
    expect(Datasource::asOptions()->toArray())->toBe([
        'ELOQUENT_BUILDER' => 'Eloquent Builder',
        'QUERY_BUILDER'    => 'Query Builder',
        'COLLECTION'       => 'Collection',
    ]);
});

test('make Datasource from string', function () {
    expect(Datasource::from('ELOQUENT_BUILDER'))->toBe(Datasource::ELOQUENT_BUILDER);
});

it('can have model', function (Datasource $case, bool $result) {
    expect($case)->canHaveModel()->toBe($result);
})->with([
    [Datasource::ELOQUENT_BUILDER, true],
    [Datasource::QUERY_BUILDER, false],
    [Datasource::COLLECTION, false],
]);

it('can read fillable', function (Datasource $case, bool $result) {
    expect($case)->canAutoImportFields()->toBe($result);
})->with([
    [Datasource::ELOQUENT_BUILDER, true],
    [Datasource::QUERY_BUILDER, true],
    [Datasource::COLLECTION, false],
]);

it('requires database table', function (Datasource $case, bool $result) {
    expect($case)->requiresDatabaseTableName()->toBe($result);
})->with([
    [Datasource::ELOQUENT_BUILDER, false],
    [Datasource::QUERY_BUILDER, true],
    [Datasource::COLLECTION, false],
]);

it('has stub', function (Datasource $case, string $stub) {
    expect($case)->stubTemplate()->toBe($stub);
})->with([
    [Datasource::ELOQUENT_BUILDER, 'table.model.stub'],
    [Datasource::QUERY_BUILDER, 'table.query-builder.stub'],
    [Datasource::COLLECTION, 'table.collection.stub'],
]);
