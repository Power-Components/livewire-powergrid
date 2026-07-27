<?php

use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Plugins\Export\Export;

function exportColumns(): array
{
    return [
        Column::make('Id', 'id'),
        Column::make('Name', 'name'),
        // Hidden + not visibleInExport => excluded from the file.
        Column::make('Secret', 'secret')->hidden(),
        // Hidden but explicitly visible in export => included.
        Column::make('Audit', 'audit')->hidden()->visibleInExport(true),
    ];
}

it('builds export headers only from exportable columns, in order', function () {
    $export = new Export();

    expect($export->exportHeaders(exportColumns()))
        ->toBe(['Id', 'Name', 'Audit']);
});

it('streams rows with values aligned to the exportable headers', function () {
    $export = new Export();

    $rows = [
        (object) ['id' => 1, 'name' => 'Pastel', 'secret' => 'x', 'audit' => 'a1'],
        (object) ['id' => 2, 'name' => 'Carne', 'secret' => 'y', 'audit' => 'a2'],
    ];

    $streamed = iterator_to_array($export->streamRows($rows, exportColumns(), stripTags: false), false);

    expect($streamed)->toBe([
        ['1', 'Pastel', 'a1'],
        ['2', 'Carne', 'a2'],
    ]);
});

it('applies stripTags and decodes html entities while streaming', function () {
    $export = new Export();

    $rows = [
        (object) ['id' => 1, 'name' => '<b>Bold</b> &amp; safe', 'secret' => '', 'audit' => ''],
    ];

    $withTags = iterator_to_array($export->streamRows($rows, exportColumns(), stripTags: false), false);
    expect($withTags[0][1])->toBe('<b>Bold</b> & safe');

    $stripped = iterator_to_array($export->streamRows($rows, exportColumns(), stripTags: true), false);
    expect($stripped[0][1])->toBe('Bold & safe');
});

it('fills missing fields and non-scalar values with an empty string', function () {
    $export = new Export();

    $rows = [
        (object) ['id' => 1, 'audit' => ['not', 'scalar']], // name missing, audit is array
    ];

    $streamed = iterator_to_array($export->streamRows($rows, exportColumns(), stripTags: false), false);

    expect($streamed)->toBe([['1', '', '']]);
});

it('keeps prepare() returning headers and rows together for backward compatibility', function () {
    $export = new Export();

    $rows = [
        (object) ['id' => 1, 'name' => 'Pastel', 'secret' => 'x', 'audit' => 'a1'],
    ];

    expect($export->prepare($rows, exportColumns(), stripTags: false))->toBe([
        'headers' => ['Id', 'Name', 'Audit'],
        'rows' => [['1', 'Pastel', 'a1']],
    ]);
});
