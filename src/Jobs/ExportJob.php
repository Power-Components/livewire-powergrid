<?php

namespace PowerComponents\LivewirePowerGrid\Jobs;

use App\Models\Dish;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use PowerComponents\LivewirePowerGrid\Services\Spout\ExportToCsv;

class ExportJob implements ShouldQueue
{
    use Batchable;
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private string $exportFileName;

    private array $columns;

    private $datasource;

    private string $type;

    private int $take;

    private int $limit;

    /**
     * @param string $exportFileName
     * @param array $columns
     * @param Collection $data
     */
    public function __construct(string $type, string $exportFileName, array $columns, $datasource, $take, $limit)
    {
        $this->type           = $type;
        $this->exportFileName = $exportFileName;
        $this->columns        = $columns;
        $this->datasource     = $datasource;
        $this->take           = $take;
        $this->limit          = $limit;
    }

    public function handle()
    {
        return (new $this->type())
            ->fileName($this->exportFileName . '-' . $this->take . '-' . $this->limit)
            ->setData($this->columns, Dish::query()->take($this->take)->limit($this->limit)->get())
            ->store();
    }
}
