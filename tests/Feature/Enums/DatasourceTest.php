<?php

use PowerComponents\LivewirePowerGrid\Enums\Datasource;

test('cases', function () {
    expect(Datasource::cases())->toBe([
        DataSource::ELOQUENT_BUILDER,
        DataSource::QUERY_BUILDER,
        DataSource::COLLECTION,
    ]);
});

it('has options', function () {
    expect(Datasource::asOptions())->toBe([
        'ELOQUENT_BUILDER' => 'Eloquent Builder',
        'QUERY_BUILDER'    => 'Query Builder',
        'COLLECTION'       => 'Collection',
    ]);
});

test('make Datasource from string', function () {
    expect(Datasource::from('ELOQUENT_BUILDER'))->toBe(DataSource::ELOQUENT_BUILDER);
});

it('can have model', function (Datasource $case, bool $result) {
    expect($case)->canHaveModel()->toBe($result);
})->with([
    [DataSource::ELOQUENT_BUILDER, true],
    [DataSource::QUERY_BUILDER, false],
    [DataSource::COLLECTION, false],
]);

it('can read fillable', function (Datasource $case, bool $result) {
    expect($case)->canReadFillable()->toBe($result);
})->with([
    [DataSource::ELOQUENT_BUILDER, true],
    [DataSource::QUERY_BUILDER, true],
    [DataSource::COLLECTION, false],
]);

it('requires database table', function (Datasource $case, bool $result) {
    expect($case)->requiresDatabaseTableName()->toBe($result);
})->with([
    [DataSource::ELOQUENT_BUILDER, false],
    [DataSource::QUERY_BUILDER, true],
    [DataSource::COLLECTION, false],
]);

it('has stub', function (Datasource $case, string $stub) {
    expect($case)->stubTemplate()->toBe($stub);
})->with([
    [DataSource::ELOQUENT_BUILDER, 'table.model.stub'],
    [DataSource::QUERY_BUILDER, 'table.query-builder.stub'],
    [DataSource::COLLECTION, 'table.collection.stub'],
]);
