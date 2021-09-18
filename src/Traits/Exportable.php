<?php

namespace PowerComponents\LivewirePowerGrid\Traits;

use PowerComponents\LivewirePowerGrid\Services\Spout\ExportToCsv;
use PowerComponents\LivewirePowerGrid\Services\Spout\ExportToXLS;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

trait Exportable
{
    public bool $exportOption = false;

    public string $exportFileName = 'download';

    public array $exportType = [];

    /**
     * @param string $fileName
     * @param array|string[] $type
     */
    public function showExportOption(string $fileName, array $type = ['excel', 'csv'])
    {
        $this->exportOption   = true;
        $this->exportFileName = $fileName;
        $this->exportType     = $type;

        return $this;
    }

    /**
     * @throws \Exception
     */
    public function prepareToExport(bool $selected = false)
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

        if ($inClause) {
            $results = $this->resolveModel()->whereIn($this->primaryKey, $inClause)->get();

            return $this->transform($results);
        }

        $results = $this->resolveModel()->get();

        return $this->transform($results);
    }

    /**
     * @throws \Exception
     */
    public function exportToXLS(bool $selected = false): BinaryFileResponse
    {
        return (new ExportToXLS())
            ->fileName($this->exportFileName)
            ->setData($this->columns(), $this->prepareToExport($selected))
            ->download();
    }

    /**
     * @throws \Exception
     */
    public function exportToCsv(bool $selected = false): BinaryFileResponse
    {
        return (new ExportToCsv())
            ->fileName($this->exportFileName)
            ->setData($this->columns(), $this->prepareToExport($selected))
            ->download();
    }
}
