<?php

namespace PowerComponents\LivewirePowerGrid\Tests\Concerns\Components;

use PowerComponents\LivewirePowerGrid\Components\SetUp\Exportable;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;

class BatchExportStorageTable extends BatchExportTable
{
    public string $exportDisk = 'powergrid-exports';

    public string $exportPath = 'exports/batches';

    public function setUp(): array
    {
        return [
            PowerGrid::exportable('export')
                ->striped()
                ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV)
                ->queues(6)
                ->disk($this->exportDisk)
                ->path($this->exportPath),

            PowerGrid::header()
                ->showSearchInput(),

            PowerGrid::footer()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }
}
