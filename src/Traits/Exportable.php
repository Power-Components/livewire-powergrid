<?php

namespace PowerComponents\LivewirePowerGrid\Traits;

use Exception;
use Illuminate\Bus\Batch;
use Illuminate\Database\Eloquent as Eloquent;
use Illuminate\Support as Support;
use PowerComponents\LivewirePowerGrid\Services\Export;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

/**
 * @property ?Batch $exportBatch
 */
trait Exportable
{
    /**
     * @throws Exception
     */
    public function prepareToExport(bool $selected = false): Eloquent\Collection|Support\Collection
    {
        $inClause = $this->filtered;

        if ($selected && filled($this->checkboxValues)) {
            $inClause = $this->checkboxValues;
        }

        if ($this->isCollection) {
            if ($inClause) {
                $results = $this->resolveCollection()->whereIn($this->primaryKey, $inClause);

                return $this->transform($results);
            }

            return $this->transform($this->resolveCollection());
        }

        $model        = $this->resolveModel();
        /** @phpstan-ignore-next-line */
        $currentTable = $model->getModel()->getTable();

        $sortField = Support\Str::of($this->sortField)->contains('.') ? $this->sortField : $currentTable . '.' . $this->sortField;

        $results = $this->resolveModel()
            ->when($inClause, function ($query, $inClause) {
                return $query->whereIn($this->primaryKey, $inClause);
            })
            ->orderBy($sortField, $this->sortDirection)
            ->get();

        return $this->transform($results);
    }

    /**
     * @throws Throwable
     */
    public function exportToXLS(bool $selected = false): BinaryFileResponse|bool
    {
        return $this->export(Export::TYPE_XLS, $selected);
    }

    /**
     * @throws Throwable
     */
    public function exportToCsv(bool $selected = false): BinaryFileResponse|bool
    {
        return $this->export(Export::TYPE_CSV, $selected);
    }

    /**
     * @throws Exception | Throwable
     *
     */
    private function export(string $exportType, bool $selected): BinaryFileResponse|bool
    {
        $exportableClass = $this->getExportableClassFromConfig($exportType);

        if ($this->queues > 0 && !$selected) {
            return $this->runOnQueue($exportableClass);
        }

        if (count($this->checkboxValues) === 0 && $selected) {
            return false;
        }

        /** @var Export $exportable */
        $exportable          = new $exportableClass();
        $currentHiddenStates = collect($this->columns)
            ->mapWithKeys(fn ($column) => [data_get($column, 'field') => data_get($column, 'hidden')]);
        $columnsWithHiddenState = array_map(function ($column) use ($currentHiddenStates) {
            $column->hidden     = $currentHiddenStates[$column->field];

            return $column;
        }, $this->columns());

        /** @var string $fileName */
        $fileName = data_get($this->setUp, 'exportable.fileName');
        $exportable
            ->fileName($fileName) /** @phpstan-ignore-next-line  */
            ->setData($columnsWithHiddenState, $this->prepareToExport($selected));

        /** @phpstan-ignore-next-line  */
        return $exportable->download($this->setUp['exportable']);
    }

    private function getExportableClassFromConfig(string $exportType): string
    {
        $defaultExportable      = strval(config('livewire-powergrid.exportable.default'));

        return strval(data_get(config('livewire-powergrid.exportable'), $defaultExportable . '.' . $exportType));
    }
}
