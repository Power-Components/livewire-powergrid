<?php

use PowerComponents\LivewirePowerGrid\Tests\Concerns\TestDatabase;
use PowerComponents\Turbine\DataSource\Support\Sql;

uses()->group('helpers', 'sql');

beforeEach(function () {
    TestDatabase::up();
});

it('throws when required arguments are missing for getSortSqlByDriver', function () {
    Sql::getSortSqlByDriver('', '', '');
})->throws(Exception::class);

it('returns driver-specific numeric sort syntax', function () {
    // MySQL >= 8.0.4 uses the REGEXP_REPLACE syntax
    expect(Sql::getSortSqlByDriver('room', 'mysql', '8.0.4'))
        ->toContain('REGEXP_REPLACE')
        ->toContain('{sortDirection}');

    // MySQL < 8.0.4 falls back to the default "+0" syntax
    expect(Sql::getSortSqlByDriver('room', 'mysql', '5.7.0'))
        ->toBe('room+0 {sortDirection}');

    // SQLite casts to integer
    expect(Sql::getSortSqlByDriver('room', 'sqlite', '3.0.0'))
        ->toContain('CAST(room AS INTEGER)');

    // An unknown driver returns the default syntax
    expect(Sql::getSortSqlByDriver('room', 'unknown-driver', '1.0.0'))
        ->toBe('room+0 {sortDirection}');
});

it('validates sort field types', function () {
    expect(Sql::isValidSortFieldType(null))->toBeFalse()
        ->and(Sql::isValidSortFieldType('varchar'))->toBeTrue()
        ->and(Sql::isValidSortFieldType('string'))->toBeTrue()
        ->and(Sql::isValidSortFieldType('integer'))->toBeFalse();
});

it('resolves the column type for a qualified sort field', function () {
    expect(Sql::getSortFieldType('name'))->toBeNull(); // no table prefix
    expect(Sql::getSortFieldType('dishes.name'))->toBeString();
});

it('throws when resolving a non-existent column type', function () {
    Sql::getSortFieldType('dishes.column_that_does_not_exist');
})->throws(Exception::class);

it('reads the database driver name and version', function () {
    expect(Sql::getDatabaseDriverName())->toBeString()->not->toBeEmpty()
        ->and(Sql::getDatabaseVersion())->toBeString()->not->toBeEmpty();
});
