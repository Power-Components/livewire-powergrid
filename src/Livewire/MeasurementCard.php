<?php

namespace PowerComponents\LivewirePowerGrid\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Lazy;

#[Lazy]
class MeasurementCard extends \Laravel\Pulse\Livewire\Card
{
    public function render(): View
    {
        $measurements = \Laravel\Pulse\Facades\Pulse::values('powergrid-measurements')
            ->map(function ($item) {
                /** @var array $value */
                $value              = json_decode($item->value, true);
                $item->tableName    = $value['tableName'];
                $item->retrieveData = $value['retrieveData'];
                $item->queriesTime  = $value['queriesTime'];
                $item->cached       = $value['cached'];
                $item->queries      = $value['queries'];
                unset($item->value);

                return $item;
            })
            ->sort(fn ($a, $b) => $b->timestamp <=> $a->timestamp)
            ->take(10)
            ->values();

        $averageRetrieveData = $measurements->avg(fn ($item) => $item->retrieveData);
        $averageQueriesTime  = $measurements->avg(fn ($item) => $item->queriesTime);

        return view(
            'livewire-powergrid::livewire.measurement-card',
            compact(
                'measurements',
                'averageQueriesTime',
                'averageRetrieveData'
            )
        );
    }
}
