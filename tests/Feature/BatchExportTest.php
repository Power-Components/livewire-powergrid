<?php

use Illuminate\Bus\PendingBatch;
use Illuminate\Support\Facades\Bus;
use Livewire\Features\SupportTesting\Testable;

use function Livewire\invade;

use PowerComponents\LivewirePowerGrid\Tests\Concerns\Components\BatchExportTable;

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
