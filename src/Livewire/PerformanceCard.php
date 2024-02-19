<?php

namespace PowerComponents\LivewirePowerGrid\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Config;
use Livewire\Attributes\Lazy;
use PowerComponents\LivewirePowerGrid\Recorders\PowerGridPerformanceRecorder;

#[Lazy]
class PerformanceCard extends \Laravel\Pulse\Livewire\Card
{
    public function render(): View
    {
        $config = Config::get('pulse.recorders.' . PowerGridPerformanceRecorder::class);

        $averageRetrieveData = 0;
        $averageQueriesTime  = 0;
        $records             = collect();

        if (data_get($config, 'enabled')) {
            $records = \Laravel\Pulse\Facades\Pulse::values('powergrid-performance')
                ->map(function ($item) {
                    /** @var array $value */
                    $value                  = json_decode($item->value, true);
                    $item->tableName        = $value['tableName'];
                    $item->retrieveDataInMs = $value['retrieveDataInMs'];
                    $item->queriesTimeInMs  = $value['queriesTimeInMs'];
                    $item->isCached         = $value['isCached'];
                    $item->queries          = $value['queries'];
                    unset($item->value);

                    return $item;
                })
                ->sort(fn ($a, $b) => $b->timestamp <=> $a->timestamp)
                ->take(10)
                ->values();

            $averageRetrieveData = $records->avg(fn ($item) => $item->retrieveDataInMs);
            $averageQueriesTime  = $records->avg(fn ($item) => $item->queriesTimeInMs);
        }

        return view(
            'livewire-powergrid::livewire.performance-card',
            compact(
                'records',
                'averageQueriesTime',
                'averageRetrieveData',
                'config'
            )
        );
    }
}
