<?php

namespace PowerComponents\LivewirePowerGrid\Jobs;

use Illuminate\Bus\{Batchable, Queueable};
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\{InteractsWithQueue, SerializesModels};
use Illuminate\Support\Facades\Crypt;
use PowerComponents\LivewirePowerGrid\Traits\ExportableJob;

/** @codeCoverageIgnore */
class ExportJob implements ShouldQueue
{
    use Batchable;
    use Dispatchable;
    use ExportableJob;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private array $properties;

    public function __construct(
        string $componentTable,
        array $columns,
        array $params
    ) {
        $this->columns         = $columns;
        $this->exportableClass = $params['exportableClass'];
        $this->fileName        = $params['fileName'];
        $this->offset          = $params['offset'];
        $this->limit           = $params['limit'];
        $this->filtered        = $params['filtered'];
        $this->exportable      = $params['exportable'];
        $this->filters         = (array) Crypt::decrypt($params['filters']);
        $this->properties      = (array) Crypt::decrypt($params['parameters']);

        $this->componentTable = new $componentTable();
    }

    public function handle(): void
    {
        $currentHiddenStates = collect($this->columns)
            ->mapWithKeys(fn ($column) => [data_get($column, 'field') => data_get($column, 'hidden')]);

        $columnsWithHiddenState = array_map(function ($column) use ($currentHiddenStates) {
            $column->hidden = $currentHiddenStates[data_get($column, 'field')];

            return $column;
        }, $this->componentTable->columns());

        /** @phpstan-ignore-next-line  */
        (new $this->exportableClass())
            ->fileName($this->getFilename())
            ->setData($columnsWithHiddenState, $this->prepareToExport($this->properties))
            ->download($this->exportable);
    }
}
