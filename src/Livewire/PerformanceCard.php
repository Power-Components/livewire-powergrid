<?php

namespace PowerComponents\LivewirePowerGrid\Livewire;

use Illuminate\Support\Facades\{Blade, Config};
use Livewire\Attributes\Lazy;
use PowerComponents\LivewirePowerGrid\Recorders\PowerGridPerformanceRecorder;

#[Lazy]
class PerformanceCard extends \Laravel\Pulse\Livewire\Card
{
    public function render()
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

        return Blade::render(<<<blade
            <x-pulse::card :cols="\$cols" :rows="\$rows" :class="\$class" wire:poll.5s="">
                <x-pulse::card-header name="PowerGrid" details="10 most recent records">
                    <x-slot:icon>⚡</x-slot:icon>
                </x-pulse::card-header>

                <x-pulse::scroll :expand="\$expand">
                    <div class="grid grid-cols-1 gap-2">
                        @if (\$records->isEmpty())
                            <x-pulse::no-results />
                        @elseif (!\$enabled)
                            <div class="h-full flex flex-col items-center justify-center p-4">
                                <x-pulse::icons.no-pulse class="h-8 w-8 stroke-gray-300 dark:stroke-gray-700" />
                                <p class="mt-2 text-sm text-gray-400 dark:text-gray-600">
                                    PowerGrid metering has been disabled.
                                </p>
                            </div>
                        @else
                            <div class="grid grid-cols-2 gap-3 text-center">
                                <div class="flex flex-col justify-center sm:block">
                                    <div class="font-bold text-gray-700 dark:text-gray-300 tabular-nums">
                                        <span class="uppercase text-xl">{{ round(\$averageRetrieveData, 2) }}</span> <span class="!text-sm">ms</span>
                                    </div>

                                    <span class="text-xs uppercase font-bold text-gray-500 dark:text-gray-400">
                                        retrieve Data
                                    </span>
                                </div>
                                <div class="flex flex-col justify-center sm:block">
                                    <div class="font-bold text-gray-700 dark:text-gray-300 tabular-nums">
                                        <span class="uppercase text-xl">{{ round(\$averageQueriesTime, 2) }}</span> <span class="!text-sm">ms</span>
                                    </div>

                                    <span class="text-xs uppercase font-bold text-gray-500 dark:text-gray-400">
                                        query Time
                                    </span>
                                </div>
                            </div>
                            <div>
                                <x-pulse::table>
                                    <colgroup>
                                        <col />
                                        <col />
                                        <col />
                                    </colgroup>
                                    <x-pulse::thead>
                                        <tr>
                                            <x-pulse::th class="text-left">Table</x-pulse::th>
                                            <x-pulse::th class="text-right">Time</x-pulse::th>
                                            <x-pulse::th class="text-right">Query Time</x-pulse::th>
                                            <x-pulse::th class="text-right">Total Queries</x-pulse::th>
                                            <x-pulse::th class="text-right">Created at</x-pulse::th>
                                        </tr>
                                    </x-pulse::thead>
                                    <tbody>
                                        @foreach (\$records as \$record)
                                            <tr wire:key="{{ \$loop->index }}-spacer" class="h-2 first:h-0"></tr>
                                            <tr wire:key="{{ \$loop->index }}-row">
                                                <x-pulse::td class="max-w-[1px]">
                                                    <code class="block text-xs text-gray-900 dark:text-gray-100 truncate" title="{{ data_get(\$record, 'tableName') }}">
                                                        {{ data_get(\$record, 'tableName') }}
                                                    </code>
                                                </x-pulse::td>
                                                <x-pulse::td numeric class="text-gray-700 dark:text-gray-300 font-bold">
                                                    {{ data_get(\$record, 'retrieveDataInMs') }} ms
                                                </x-pulse::td>
                                                <x-pulse::td numeric class="text-gray-700 dark:text-gray-300 font-bold">
                                                    {{ data_get(\$record, 'queriesTimeInMs') }} ms
                                                </x-pulse::td>
                                                <x-pulse::td numeric class="text-gray-700 dark:text-gray-300 font-bold">
                                                    {{ count(data_get(\$record, 'queries') ) }}
                                                </x-pulse::td>
                                                <x-pulse::td numeric class="text-gray-700 text-sm dark:text-gray-300 font-bold">

                                                {{ \\Illuminate\Support\Carbon::createFromTimestamp(data_get(\$record, 'timestamp'))->diffForHumans() }}
                                                </x-pulse::td>
                                             </tr>
                                        @endforeach
                                    </tbody>
                                </x-pulse::table>
                            </div>
                        @endif
                    </div>
                </x-pulse::scroll>
            </x-pulse::card>
        blade, [
            'cols'                => $this->cols,
            'rows'                => $this->rows,
            'class'               => $this->class,
            'expand'              => $this->expand,
            'averageRetrieveData' => $averageRetrieveData,
            'averageQueriesTime'  => $averageQueriesTime,
            'records'             => $records,
            'enabled'             => $config['enabled'],
        ]);
    }
}
