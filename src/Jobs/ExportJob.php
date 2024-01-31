<?php

namespace PowerComponents\LivewirePowerGrid\Jobs;

use Illuminate\Bus\{Batchable, Queueable};
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\{InteractsWithQueue, SerializesModels};
use Illuminate\Support\Facades\Crypt;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\Traits\ExportableJob;

/** @codeCoverageIgnore */
class ExportJob implements ShouldQueue
{
    use Batchable;
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use ExportableJob;

    private array $properties;

    /**
     * @param string $componentTable
     * @param array $columns
     * @param array $params
     */
    public function __construct(
        string $componentTable,
        array  $columns,
        array  $params
    ) {
        $this->columns         = $columns;
        $this->exportableClass = $params['exportableClass'];
        $this->fileName        = $params['fileName'];
        $this->offset          = $params['offset'];
        $this->limit           = $params['limit'];
        $this->filters         = (array) Crypt::decrypt($params['filters']);
        $this->properties      = (array) Crypt::decrypt($params['parameters']);

        /** @var PowerGridComponent $componentTable */
        $this->componentTable = new $componentTable();
    }

    public function handle(): void
    {
        $exportable = new $this->exportableClass();

        $currentHiddenStates = collect($this->columns)
            ->mapWithKeys(fn ($column) => [data_get($column, 'field') => data_get($column, 'hidden')]);

        $columnsWithHiddenState = array_map(function ($column) use ($currentHiddenStates) {
            $column->hidden = $currentHiddenStates[$column->field];

            return $column;
        }, $this->componentTable->columns());

        /** @phpstan-ignore-next-line  */
        $exportable
            ->fileName($this->getFilename())
            ->setData($columnsWithHiddenState, $this->prepareToExport($this->properties))
            ->download([]);
    }
}
