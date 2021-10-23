<?php

namespace PowerComponents\LivewirePowerGrid\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use PowerComponents\LivewirePowerGrid\Traits\ExportableJob;

class ExportJob implements ShouldQueue
{
    use Batchable;
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use ExportableJob;

    /**
     * @param string $componentTable
     * @param array $columns
     * @param array $params
     */
    public function __construct(
        string $componentTable,
        array $columns,
        array $params
    ) {
        $this->columns  = $columns;
        $this->type     = data_get($params, 'type');
        $this->fileName = data_get($params, 'fileName');
        $this->offset   = data_get($params, 'offset');
        $this->limit    = data_get($params, 'limit');

        $this->componentTable = new $componentTable();
    }

    public function handle()
    {
        $query = $this->componentTable
            ->datasource()
            ->offset($this->offset)
            ->limit($this->limit)
            ->get();

        return (new $this->type())
            ->fileName($this->getFilename())
            ->setData($this->columns, $this->transform($query))
            ->store();
    }
}
