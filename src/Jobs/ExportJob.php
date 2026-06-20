<?php

namespace PowerComponents\LivewirePowerGrid\Jobs;

use Illuminate\Bus\{Batchable, Queueable};
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\{InteractsWithQueue, SerializesModels};
use Illuminate\Support\Facades\Crypt;
use PowerComponents\LivewirePowerGrid\Column;
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

    /** @var array<string, mixed> */
    private array $properties;

    /**
     * @param  array<int, Column>  $columns
     * @param  array<string, mixed>  $params
     */
    public function __construct(
        string $componentTable,
        array $columns,
        array $params
    ) {
        $this->columns = $columns;
        $this->exportableClass = strval($params['exportableClass'] ?? '');
        $this->fileName = strval($params['fileName'] ?? '');
        $this->offset = intval($params['offset'] ?? 0);
        $this->limit = intval($params['limit'] ?? 0);
        $filteredParam = is_array($params['filtered'] ?? null) ? $params['filtered'] : [];
        $this->filtered = array_values(array_filter($filteredParam, fn ($v) => is_int($v) || is_string($v)));
        $this->exportable = is_array($params['exportable'] ?? null) ? $params['exportable'] : [];
        /** @phpstan-ignore-next-line */
        $this->filters = (array) Crypt::decrypt($params['filters'] ?? '');
        /** @phpstan-ignore-next-line */
        $this->properties = (array) Crypt::decrypt($params['parameters'] ?? '');

        /** @phpstan-ignore assign.propertyType */
        $this->componentTable = new $componentTable();

        $this->componentTable->isExporting = true;
    }

    public function handle(): void
    {
        collect($this->componentTable->getPublicPropertiesDefinedInComponent())
            ->intersectByKeys($this->properties)
            ->each(fn ($value, $key) => $this->componentTable->{$key} = data_get($this->properties, $key));

        $currentHiddenStates = collect($this->columns)
            ->mapWithKeys(fn ($column) => [strval(data_get($column, 'field')) => data_get($column, 'hidden')]);

        $columnsWithHiddenState = array_map(function ($column) use ($currentHiddenStates) {
            data_set($column, 'hidden', data_get($currentHiddenStates, data_get($column, 'field'), true));

            return $column;
        }, $this->componentTable->columns());

        $exportableInstance = new $this->exportableClass();
        /** @phpstan-ignore method.notFound */
        $exportable = $exportableInstance->fileName($this->getFilename())
            ->setData($columnsWithHiddenState, $this->prepareToExport($this->properties));

        if (method_exists($exportable, 'store')) {
            $exportable->store($this->exportable);
        } else {
            $exportable->download($this->exportable);
        }
    }
}
