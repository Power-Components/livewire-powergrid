<?php

namespace PowerComponents\LivewirePowerGrid\Jobs;

use App\Http\Livewire\DishesTable;
use App\Models\Dish;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

class ExportJob implements ShouldQueue
{
    use Batchable;
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private string $exportFileName;

    private PowerGridComponent $componentTable;

    private array $columns;

    private $datasource;

    private string $type;

    private int $offset;

    private int $limit;

    /**
     * @param string $componentTable
     * @param string $type
     * @param string $exportFileName
     * @param array $columns
     * @param $datasource
     * @param $offset
     * @param $limit
     */
    public function __construct(
        string $componentTable,
        string $type,
        string $exportFileName,
        array $columns,
        $offset,
        $limit
    ) {
        $this->type           = $type;
        $this->exportFileName = $exportFileName;
        $this->columns        = $columns;
        $this->offset         = $offset;
        $this->limit          = $limit;

        $this->componentTable = new $componentTable();
    }

    public function handle()
    {
        $fileName = $this->exportFileName . '-' . $this->offset . '-' . $this->limit;

        $query = $this->componentTable
            ->datasource()
            ->offset($this->offset)
            ->limit($this->limit)
            ->get();

        return (new $this->type())
            ->fileName($fileName)
            ->setData($this->columns, $this->transform($query))
            ->store();
    }

    private function transform($results)
    {
        return $results->transform(function ($row) {
            $row = (object)$row;
            $columns = $this->componentTable->addColumns()->columns;
            foreach ($columns as $key => $column) {
                $row->{$key} = $column($row);
            }

            return $row;
        });
    }
}
