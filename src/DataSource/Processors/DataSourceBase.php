<?php

namespace PowerComponents\LivewirePowerGrid\DataSource\Processors;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\{Builder as EloquentBuilder};
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Pagination\Paginator;
use Illuminate\View\Concerns\ManagesLoops;
use PowerComponents\LivewirePowerGrid\{Concerns\SoftDeletes, PowerGridComponent};

class DataSourceBase
{
    use ManagesLoops;
    use SoftDeletes;

    public static array $actionsHtml = [];

    public function __construct(
        public PowerGridComponent $component,
        public bool               $isExport = false
    ) {
    }

    public function prepareDataSource(): mixed
    {
        return $this->component->datasource($this->component->properties ?? []);
    }

    protected function setTotalCount(EloquentBuilder|MorphToMany|QueryBuilder|LengthAwarePaginator|Paginator $results): void
    {
        if (!method_exists($results, 'total')) {
            return;
        }

        $this->component->total = $results->total();
    }

    protected function setCurrentTable(mixed $datasource): void
    {
        if ($datasource instanceof QueryBuilder) {
            /** @var string $from */
            $from                          = $datasource->from;
            $this->component->currentTable = $from;

            return;
        }

        /** @phpstan-ignore-next-line */
        $this->component->currentTable = $datasource->getModel()->getTable();
    }
}
