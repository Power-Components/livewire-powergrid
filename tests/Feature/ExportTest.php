<?php

use OpenSpout\Reader\XLSX\Reader;
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Components\ExportTable;

use function PowerComponents\LivewirePowerGrid\Tests\Plugins\livewire;

it('properly export xls - all data', function () {
    livewire(ExportTable::class)
        ->call('exportToXLS', false)
        ->assertFileDownloaded('export.xlsx');
})->requiresOpenSpout();

it('properly does not export xls data without selected data', function () {
    $component = livewire(ExportTable::class)
        ->call('exportToXLS', true);

    expect()->notToBeFileDownloaded($component);
})->requiresOpenSpout();

it('properly export csv data with selected data', function () {
    $downloadedFile = livewire(ExportTable::class)
         ->set('checkboxValues', [
             0 => '1',
             1 => '2',
         ])
         ->call('exportToCsv', true);

    $headings = ['ID', 'Prato'];

    $rows = [
        ['1', 'Pastel de Nata'],
        ['2', 'Peixada da chef Nábia'],
    ];

    expect($downloadedFile)->toBeCsvDownload($headings, $rows);
})->requiresOpenSpout();

it('properly export xls data with selected data', function () {
    $downloadedFile = livewire(ExportTable::class)
        ->set('checkboxValues', [
            0 => '1',
            1 => '2',
            2 => '5',
        ])
        ->call('exportToXLS', true);

    $headings = ['ID', 'Prato'];

    $rows = [
        ['1', 'Pastel de Nata'],
        ['2', 'Peixada da chef Nábia'],
        ['5', 'Francesinha vegana'],
    ];

    expect($downloadedFile)->toBeXLSDownload($headings, $rows);
})->requiresOpenSpout();

it('properly sets CSV separator and delimiter', function () {
    $downloadedFile = livewire(ExportTable::class, ['separator' => '|', 'delimiter' => '@'])
       ->set('checkboxValues', [
           0 => '1',
       ])
       ->call('exportToCsv', true);

    $headings = ['ID', 'Prato'];

    $rows = [
        ['1', 'Pastel de Nata'],
    ];

    expect($downloadedFile)->toBeCsvDownload($headings, $rows);
})->requiresOpenSpout();

it('properly export csv - all data', function () {
    livewire(ExportTable::class)
        ->call('exportToCsv', false)
        ->assertFileDownloaded('export.csv');
})->requiresOpenSpout();

it('properly does not export csv data without selected data', function () {
    $component = livewire(ExportTable::class)
        ->call('exportToCsv', true);

    expect()->notToBeFileDownloaded($component);
})->requiresOpenSpout();

/*
|--------------------------------------------------------------------------
| Expectations for this test
|--------------------------------------------------------------------------
|
*/

expect()->extend('notToBeFileDownloaded', function ($component) {
    $downloadEffect = data_get($component->effects, 'download');

    expect($downloadEffect)->toBeNull();
});

expect()->extend('toBeCsvDownload', function (array $headings, array $rows) {
    $downloadEffect = data_get($this->value->effects, 'download');

    $filename  = data_get($this->value->setUp, 'exportable.fileName') . '.csv';
    $separator = data_get($this->value->setUp, 'exportable.csvSeparator', ',');
    $delimiter = data_get($this->value->setUp, 'exportable.csvDelimiter', '"');

    test()->assertEquals(
        $filename,
        data_get($downloadEffect, 'name')
    );

    $content = str_replace(PHP_EOL, '<csv-divider>', base64_decode(data_get($downloadEffect, 'content')));

    $expected = collect(array_merge([$headings], $rows))
        ->transform(function ($heading) use ($delimiter, $separator) {
            $heading = collect($heading)->transform(fn ($heading) => trim($heading, $delimiter))->toArray();

            return implode($separator, $heading);
        });

    $actual = collect(explode('<csv-divider>', $content))
        ->filter(fn ($heading) => strlen($heading) > 0)
        ->transform(function ($line) use ($delimiter, $separator) {
            $arrayLine = explode($separator, preg_replace('/[^A-Za-z0-9.!?|@,á" ]/', '', $line ?? ''));

            $rows = collect($arrayLine)
                ->transform(fn ($row) => trim($row, $delimiter))
                ->toArray();

            return implode($separator, $rows);
        });

    test()->assertEquals(
        $expected,
        $actual
    );
});

expect()->extend('toBeXLSDownload', function (array $headings, array $rows) {
    $downloadEffect = data_get($this->value->effects, 'download');

    $filename  = data_get($this->value->setUp, 'exportable.fileName') . '.xlsx';
    $separator = data_get($this->value->setUp, 'exportable.csvSeparator', ',');
    $delimiter = data_get($this->value->setUp, 'exportable.csvDelimiter', '"');

    test()->assertEquals(
        $filename,
        data_get($downloadEffect, 'name')
    );

    $content = base64_decode(data_get($downloadEffect, 'content'));

    file_put_contents(base_path($filename), $content);

    $reader = new Reader();
    $reader->open(base_path($filename));

    $testableRows = [];

    foreach ($reader->getSheetIterator() as $sheet) {
        foreach ($sheet->getRowIterator() as $row) {
            foreach ($row->getCells() as $cell) {
                $testableRows[] = $cell->getValue();
            }
        }
    }

    $reader->close();

    $content = implode(',', $testableRows);

    $expected = collect(array_merge([$headings], $rows))
        ->transform(function ($heading) use ($delimiter, $separator) {
            $heading = collect($heading)->transform(fn ($heading) => trim($heading, $delimiter))->toArray();

            return implode($separator, $heading);
        });

    $actual = collect($content)
        ->filter(fn ($heading) => strlen($heading) > 0)
        ->transform(function ($line) use ($delimiter, $separator) {
            $arrayLine = explode($separator, preg_replace('/[^A-Za-z0-9.!?|@,á" ]/', '', $line ?? ''));

            $rows = collect($arrayLine)
                ->transform(fn ($row) => trim($row, $delimiter))
                ->toArray();

            return implode($separator, $rows);
        });

    test()->assertEquals(
        implode(',', $expected->toArray()),
        implode(',', $actual->toArray())
    );
});
