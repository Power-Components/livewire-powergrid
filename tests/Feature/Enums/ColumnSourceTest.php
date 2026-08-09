<?php

use PowerComponents\LivewirePowerGrid\Commands\Enums\ColumnSource;

test('cases', function () {
    expect(ColumnSource::cases())->toBe([
        ColumnSource::FILLABLE,
        ColumnSource::DATABASE_TABLE,
    ]);
});

test('make ColumnSource from string', function (string $name, ColumnSource $case) {
    expect(ColumnSource::from($name))->toBe($case);
})->with([
    ['FILLABLE', ColumnSource::FILLABLE],
    ['DATABASE_TABLE', ColumnSource::DATABASE_TABLE],
]);
