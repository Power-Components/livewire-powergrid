<?php

use Illuminate\Bus\PendingBatch;
use Illuminate\Support\Facades\{Bus, Storage};
use Livewire\Features\SupportTesting\Testable;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\Support\ExportStorage;
use PowerComponents\LivewirePowerGrid\Tests\Concerns\Components\{BatchExportStorageTable, BatchExportTable};

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

it('can configure export storage disk and path.', function () {
    $exportable = PowerGrid::exportable('orders')
        ->disk('azure')
        ->path('/exports/orders/');

    expect($exportable->toLivewire())
        ->toMatchArray([
            'disk' => 'azure',
            'path' => '/exports/orders/',
        ])
        ->and(ExportStorage::filePath($exportable, 'orders.csv'))
        ->toBe('exports/orders/orders.csv');
});

it('stores queued csv exports on the configured storage disk and path.', function () {
    Storage::fake('powergrid-exports');
    Bus::fake();

    livewire(BatchExportStorageTable::class, [
        'filterDataSourceId' => 77,
    ])->call('exportToCsv', false);

    Bus::assertBatched(function (PendingBatch $batch) {
        $jobs = $batch->jobs[0];

        $jobs[0]->handle();

        return true;
    });

    $files = Storage::disk('powergrid-exports')->files('exports/batches');

    expect($files)->toHaveCount(1)
        ->and(str_starts_with($files[0], 'exports/batches/export-'))->toBeTrue()
        ->and(str_ends_with($files[0], '.csv'))->toBeTrue();
})->requiresOpenSpout();

it('uses export storage config defaults when per table storage is not configured.', function () {
    Storage::fake('powergrid-exports');
    Bus::fake();

    config([
        'livewire-powergrid.exportable.storage.disk' => 'powergrid-exports',
        'livewire-powergrid.exportable.storage.path' => '/configured/exports/',
    ]);

    livewire(BatchExportTable::class, [
        'filterDataSourceId' => 77,
    ])->call('exportToCsv', false);

    Bus::assertBatched(function (PendingBatch $batch) {
        $jobs = $batch->jobs[0];

        $jobs[0]->handle();

        return true;
    });

    $files = Storage::disk('powergrid-exports')->files('configured/exports');

    expect($files)->toHaveCount(1)
        ->and(str_starts_with($files[0], 'configured/exports/export-'))->toBeTrue()
        ->and(str_ends_with($files[0], '.csv'))->toBeTrue();
})->requiresOpenSpout();

it('downloads queued exports from the configured storage disk and path.', function () {
    Storage::fake('powergrid-exports');
    Storage::disk('powergrid-exports')->put('exports/batches/export.csv', 'id,name');

    livewire(BatchExportStorageTable::class, [
        'filterDataSourceId' => 77,
    ])
        ->set('exportedFiles', ['export.csv'])
        ->call('downloadExport', 'export.csv')
        ->assertFileDownloaded('export.csv');
})->requiresOpenSpout();
