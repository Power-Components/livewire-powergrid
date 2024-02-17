<?php

namespace PowerComponents\LivewirePowerGrid\Recorders;

use Carbon\CarbonImmutable;
use Illuminate\Config\Repository;
use Laravel\Pulse\Pulse;
use PowerComponents\LivewirePowerGrid\Events\MeasureRetrieveData;

class PowerGridRecorder
{
    public string $listen = MeasureRetrieveData::class;

    public function __construct(
        protected Pulse $pulse,
        protected Repository $config
    ) {
    }

    public function record(MeasureRetrieveData $class): void
    {
        $now = CarbonImmutable::now();

        $measurement = collect($class);

        $this->pulse->set(
            type: 'powergrid-measurements',
            key: uniqid(),
            value: $measurement,
            timestamp: $now->getTimestamp()
        );
    }
}
