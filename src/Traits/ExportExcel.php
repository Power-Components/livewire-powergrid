<?php


namespace PowerComponents\LivewirePowerGrid\Traits;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use PowerComponents\LivewirePowerGrid\Services\Export;
use PowerComponents\LivewirePowerGrid\Services\ExportToXLS;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

trait ExportExcel
{
    /**
     * @throws \Exception
     */
    public function exportToExcel(): BinaryFileResponse
    {
        return (new ExportToXLS())
            ->fileName('excel')
            ->fromCollection($this->collection())
            ->columns($this->columns())
            ->checkedValues($this->checkbox_values)
            ->export();
    }

    public function exportToCSV()
    {
        return false;
    }
}
