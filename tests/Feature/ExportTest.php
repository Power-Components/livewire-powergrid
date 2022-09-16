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
