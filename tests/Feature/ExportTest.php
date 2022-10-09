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
    $downloadedFile =  livewire(ExportTable::class)
         ->set('checkboxValues', [
             0 => '1',
             1 => '2',
         ])
         ->call('exportToCsv', true);

    expect($downloadedFile)->toBeCsvWithContent('export.csv', 'ID,Prato1,"Pastel de Nata"2,"Peixada da chef Nábia"');
});

it('properly sets CSV separator and delimiter', function () {
    $downloadedFile = livewire(ExportTable::class, ['separator' => '|', 'delimiter' => '@'])
       ->set('checkboxValues', [
           0 => '1',
       ])
       ->call('exportToCsv', true);

    expect($downloadedFile)->toBeCsvWithContent('export.csv', 'ID|Prato1|@Pastel de Nata@');
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

    $regex = '/[^A-Za-z0-9.!?|@,á" ]/';

    test()->assertEquals(
        preg_replace($regex, '', $content),
        preg_replace($regex, '', base64_decode(data_get($downloadEffect, 'content'))),
    );
});
