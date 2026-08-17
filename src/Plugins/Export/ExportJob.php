<?php

namespace PowerComponents\LivewirePowerGrid\Plugins\Export;

use Illuminate\Bus\{Batchable, Queueable};
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\{InteractsWithQueue, SerializesModels};
use Illuminate\Support\Facades\Crypt;
use PowerComponents\LivewirePowerGrid\{Column, PowerGridComponent};
use PowerComponents\LivewirePowerGrid\Plugins\Export\Concerns\ExportableJob;
use PowerComponents\LivewirePowerGrid\Plugins\Export\Contracts\ExportInterface;

/** @codeCoverageIgnore */
class ExportJob implements ShouldQueue
{
    use Batchable;
    use Dispatchable;
    use ExportableJob;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /** @var array<mixed, mixed> */
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
        /** @var string $exportableClass */
        $exportableClass = $params['exportableClass'] ?? '';
        $this->exportableClass = $exportableClass;
        /** @var string $fileName */
        $fileName = $params['fileName'] ?? '';
        $this->fileName = $fileName;
        /** @var int $offset */
        $offset = $params['offset'] ?? 0;
        $this->offset = $offset;
        /** @var int $limit */
        $limit = $params['limit'] ?? 0;
        $this->limit = $limit;
        $filteredParam = is_array($params['filtered'] ?? null) ? $params['filtered'] : [];
        $this->filtered = array_values(array_filter($filteredParam, fn ($v) => is_int($v) || is_string($v)));
        $exportableParam = $params['exportable'] ?? [];
        $this->exportable = is_object($exportableParam) ? (array) $exportableParam : (is_array($exportableParam) ? $exportableParam : []);
        /** @var string $filters */
        $filters = $params['filters'] ?? '';
        $this->filters = (array) Crypt::decrypt($filters);
        /** @var string $parameters */
        $parameters = $params['parameters'] ?? '';
        $this->properties = (array) Crypt::decrypt($parameters);

        /** @var PowerGridComponent $tableInstance */
        $tableInstance = new $componentTable();
        $this->componentTable = $tableInstance;

        $this->componentTable->isExporting = true;
    }

    public function handle(): void
    {
        collect($this->componentTable->getPublicPropertiesDefinedInComponent())
            ->intersectByKeys($this->properties)
            ->each(fn ($value, $key) => $this->componentTable->{$key} = data_get($this->properties, $key));

        $currentHiddenStates = collect($this->columns)
            ->mapWithKeys(function ($column) {
                /** @var string $field */
                $field = data_get($column, 'field');

                return [$field => data_get($column, 'hidden')];
            });

        /** @var array<int, Column> $columnsWithHiddenState */
        $columnsWithHiddenState = array_map(function ($column) use ($currentHiddenStates) {
            /** @var string|null $field */
            $field = data_get($column, 'field');
            data_set($column, 'hidden', data_get($currentHiddenStates, $field, true));

            return $column;
        }, $this->componentTable->columns());

        /** @var Export&ExportInterface $exportableInstance */
        $exportableInstance = new $this->exportableClass();
        /** @var Export&ExportInterface $exportable */
        $exportable = $exportableInstance->fileName($this->getFilename())
            ->setData($columnsWithHiddenState, $this->prepareToExport($this->properties));

        if (method_exists($exportable, 'store')) {
            $exportable->store($this->exportable);
        } else {
            $exportable->download($this->exportable);
        }
    }
}
