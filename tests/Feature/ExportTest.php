<?php

use function Pest\Livewire\livewire;

use PowerComponents\LivewirePowerGrid\Tests\ExportTable;

it('properly export xls data with selected data', function () {
    livewire(ExportTable::class)
        ->set('checkboxValues', [
            0 => '1',
            1 => '2',
        ])
        ->call('exportToXLS', true)
        ->assertFileDownloaded('export.xlsx');
});

it('properly export xls - all data', function () {
    livewire(ExportTable::class)
        ->call('exportToXLS', false)
        ->assertFileDownloaded('export.xlsx');
});

it('properly does not export xls data without selected data', function () {
    $component = livewire(ExportTable::class)
        ->call('exportToXLS', true);

    expect(null)->notToBeFileDownloaded($component);
});

it('properly export csv data with selected data', function () {
    livewire(ExportTable::class)
        ->set('checkboxValues', [
            0 => '1',
            1 => '2',
        ])
        ->call('exportToCsv', true)
        ->assertFileDownloaded('export.csv');
});

it('properly sets CSV separator and delimiter', function () {
    $component = livewire(ExportTable::class, ['separator' => '|', 'delimiter' => '@'])
       ->set('checkboxValues', [
           0 => '1',
       ])
       ->call('exportToCsv', true);

    expect($component)->toBeCsvWithContent('export.csv', 'ID|Prato1|@Pastel de Nata@');
});

it('properly export csv - all data', function () {
    livewire(ExportTable::class)
        ->call('exportToCsv', false)
        ->assertFileDownloaded('export.csv');
});

it('properly does not export csv data without selected data', function () {
    $component = livewire(ExportTable::class)
        ->call('exportToCsv', true);

    expect(null)->notToBeFileDownloaded($component);
});

/*
|--------------------------------------------------------------------------
| Expectations for this test
|--------------------------------------------------------------------------
|
*/

expect()->extend('notToBeFileDownloaded', function ($component) {
    $downloadEffect = data_get($component->lastResponse, 'original.effects.download');

    expect($downloadEffect)->toBeNull();
});

expect()->extend('toBeCsvWithContent', function (string $filename, $content) {
    $downloadEffect = data_get($this->value->lastResponse, 'original.effects.download');

    test()->assertEquals(
        $filename,
        data_get($downloadEffect, 'name')
    );

    test()->assertEquals(
        preg_replace('/[^A-Za-z0-9.!?|@]/', '', $content),
        preg_replace('/[^A-Za-z0-9.!?|@]/', '', base64_decode(data_get($downloadEffect, 'content'))),
    );
});
