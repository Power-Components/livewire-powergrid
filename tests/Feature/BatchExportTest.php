<?php

use Illuminate\Bus\PendingBatch;
use Illuminate\Support\Facades\Bus;
use Livewire\Features\SupportLockedProperties\CannotUpdateLockedPropertyException;
use Livewire\Features\SupportTesting\Testable;
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Components\BatchExportTable;

use function Livewire\invade;
use function PowerComponents\LivewirePowerGrid\Tests\Plugins\livewire;

it('can pass class parameters in batch export.', function () {
    Bus::fake();

    /** @var Testable $component */
    $component = livewire(BatchExportTable::class, [
        'filterDataSourceId' => 77,
    ])
        ->call('exportToXLS', false);

    $getPublicPropertiesDefinedInComponent = $component->instance()->getPublicPropertiesDefinedInComponent();

    Bus::assertBatched(function (PendingBatch $batch) use ($getPublicPropertiesDefinedInComponent) {
        $jobs = $batch->jobs[0];

        $properties = invade($jobs[0])->properties;

        return $getPublicPropertiesDefinedInComponent['filterDataSourceId'] ===
            $properties['filterDataSourceId'];
    });
})->requiresOpenSpout();

it('prevents the client from overwriting export state', function (string $property, $payload) {
    expect(function () use ($property, $payload) {
        livewire(BatchExportTable::class, ['filterDataSourceId' => 1])
            ->set($property, $payload);
    })->toThrow(CannotUpdateLockedPropertyException::class);
})->with([
    'exportedFiles' => ['exportedFiles', ['file.xlsx']],
    'exportableJobClass' => ['exportableJobClass', 'Foo\\Bar'],
]);

it('does not download a file that is not part of the export', function (string $path) {
    $component = livewire(BatchExportTable::class, ['filterDataSourceId' => 1])
        ->call('downloadExport', $path);

    expect(data_get($component->effects, 'download'))->toBeNull();
})->with([
    'parent' => ['../up.txt'],
    'grandparent' => ['../../up-two.txt'],
    'absolute' => ['/absolute/path.txt'],
    'nested' => ['a/../../b.txt'],
]);

it('does not download a file that exists in storage but was not exported', function () {
    $file = 'other-file.txt';
    $storagePath = storage_path($file);

    file_put_contents($storagePath, 'data');

    try {
        $component = livewire(BatchExportTable::class, ['filterDataSourceId' => 1])
            ->call('downloadExport', $file);

        expect(data_get($component->effects, 'download'))->toBeNull();
    } finally {
        @unlink($storagePath);
    }
});

it('does not download a path that is not a plain filename, even when it is listed', function (string $relative) {
    $target = storage_path($relative);
    @mkdir(dirname($target), 0777, true);
    file_put_contents($target, 'data');

    try {
        $component = livewire(BatchExportTable::class, [
            'filterDataSourceId' => 1,
            'exportedFiles' => [$relative],
        ])->call('downloadExport', $relative);

        expect(data_get($component->effects, 'download'))->toBeNull();
    } finally {
        @unlink($target);
        @rmdir(dirname($target));
    }
})->with([
    'subdirectory' => ['sub/other-file.txt'],
    'parent' => ['../other-file.txt'],
]);

it('downloads a file produced by the export', function () {
    $file = 'testing-batch-export-1-1-fake.xlsx';
    $storagePath = storage_path($file);

    file_put_contents($storagePath, 'dummy');

    try {
        livewire(BatchExportTable::class, [
            'filterDataSourceId' => 1,
            'exportedFiles' => [$file],
        ])
            ->call('downloadExport', $file)
            ->assertFileDownloaded($file);
    } finally {
        @unlink($storagePath);
    }
});
