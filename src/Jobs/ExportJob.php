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
        $this->columns = $columns;
        $this->exportableClass = $params['exportableClass'];
        $this->fileName = $params['fileName'];
        $this->offset = $params['offset'];
        $this->limit = $params['limit'];
        $this->filtered = $params['filtered'];
        $this->exportable = $params['exportable'];
        $this->filters = (array) Crypt::decrypt($params['filters']);
        $this->properties = (array) Crypt::decrypt($params['parameters']);

        $this->componentTable = new $componentTable();

        $this->componentTable->isExporting = true;
    }

    public function handle(): void
    {
        collect($this->properties)
            ->each(function ($value, $key): void {
                if (property_exists($this->componentTable, $key)) {
                    $this->componentTable->{$key} = $value;
                }
            });

        $currentHiddenStates = collect($this->columns)
            ->mapWithKeys(fn ($column) => [data_get($column, 'field') => data_get($column, 'hidden')]);

        $columnsWithHiddenState = array_map(function ($column) use ($currentHiddenStates) {
            $column->hidden = data_get($currentHiddenStates, data_get($column, 'field'), true);

            return $column;
        }, $this->componentTable->columns());

        $exportable = (new $this->exportableClass())
            ->fileName($this->getFilename())
            ->setData($columnsWithHiddenState, $this->prepareToExport($this->properties));

        if (method_exists($exportable, 'store')) {
            $exportable->store($this->exportable);

            return;
        }

        $exportable->download($this->exportable);
    }
}
