<?php

use PowerComponents\LivewirePowerGrid\Helpers\SqlSupport;

it('finds database driver name', function () {
    expect(SqlSupport::getDatabaseDriverName())->not->toBeNull();
});

it('finds database version', function () {
    expect(SqlSupport::getDatabaseVersion())->not->toBeNull();
});

it('returns the proper "LIKE" syntax', function () {
    $driver = SqlSupport::getDatabaseDriverName();

    expect(SqlSupport::like())
        ->when(
            $driver === 'mysql',
            fn ($syntax) => $syntax->toBe('LIKE')
        )
        ->when(
            $driver === 'sqlite',
            fn ($syntax) => $syntax->toBe('LIKE')
        )
        ->when(
            $driver === 'sqlsrv',
            fn ($syntax) => $syntax->toBe('LIKE')
        )
        ->when(
            $driver === 'pgsql',
            fn ($syntax) => $syntax->toBe('ILIKE')
        )
        ->not->toBeNull();
});

it('returns sortField', function (array $data) {
    expect(SqlSupport::getSortSqlByDriver('field', $data['db'], $data['version']))
        ->toBe($data['expected']);
})->with([
    [['db' => 'sqlite', 'version' => '3.36.0',  'expected' => 'CAST(field AS INTEGER)']],
    [['db' => 'mysql', 'version' => '5.5.59-MariaDB',  'expected' => 'field+0']],
    [['db' => 'mysql', 'version' => '5.4.1',  'expected' => 'field+0']],
    [['db' => 'mysql', 'version' => '5.7.36', 'expected' => 'field+0']],
    [['db' => 'mysql', 'version' => '8.0.3',  'expected' => 'field+0']],
    [['db' => 'mysql', 'version' => '8.0.4',  'expected' => "CAST(NULLIF(REGEXP_REPLACE(field, '[[:alpha:]]+', ''), '') AS SIGNED INTEGER)"]],
    [['db' => 'mysql', 'version' => '8.0.5',  'expected' => "CAST(NULLIF(REGEXP_REPLACE(field, '[[:alpha:]]+', ''), '') AS SIGNED INTEGER)"]],
    [['db' => 'pgsql', 'version' => '9.6.24',  'expected' => "CAST(NULLIF(REGEXP_REPLACE(field, '\D', '', 'g'), '') AS INTEGER)"]],
    [['db' => 'pgsql', 'version' => '13.5',  'expected' => "CAST(NULLIF(REGEXP_REPLACE(field, '\D', '', 'g'), '') AS INTEGER)"]],
    [['db' => 'pgsql', 'version' => '15.5',  'expected' => "CAST(NULLIF(REGEXP_REPLACE(field, '\D', '', 'g'), '') AS INTEGER)"]],
    [['db' => 'sqlsrv', 'version' => '9.2',  'expected' => "CAST(SUBSTRING(field, PATINDEX('%[a-z]%', field), LEN(field)-PATINDEX('%[a-z]%', field)) AS INT)"]],
    [['db' => 'sqlsrv', 'version' => '14.00.3421',  'expected' => "CAST(SUBSTRING(field, PATINDEX('%[a-z]%', field), LEN(field)-PATINDEX('%[a-z]%', field)) AS INT)"]],
    [['db' => 'sqlsrv', 'version' => '20.80',  'expected' => "CAST(SUBSTRING(field, PATINDEX('%[a-z]%', field), LEN(field)-PATINDEX('%[a-z]%', field)) AS INT)"]],
    [['db' => 'unsupported-db', 'version' => '29.00.00',  'expected' => 'field+0']],

]);
