<?php

namespace PowerComponents\LivewirePowerGrid\DataSource\Processors;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Query\Builder as QueryBuilder;
use PowerComponents\LivewirePowerGrid\DataSource\{Builder, DataSourceProcessorInterface};
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection as BaseCollection;

class ModelProcessor extends DataSourceBase implements DataSourceProcessorInterface
{
    public static function match(mixed $key): bool
    {
        return true;
    }

    /**
     * @throws \Throwable
     */
    public function process(): BaseCollection|LengthAwarePaginator|\Illuminate\Contracts\Pagination\LengthAwarePaginator|Paginator|MorphToMany
    {
        $this->setCurrentTable($this->prepareDataSource());

        $results = $this->prepareDataSource()
            ->where(
                fn (EloquentBuilder|QueryBuilder $query) => Builder::make($query, $this->component)
                    ->filterContains()
                    ->filter()
            );

        if ($this->prepareDataSource() instanceof EloquentBuilder || $this->prepareDataSource() instanceof MorphToMany) {
            $results = $this->applySoftDeletes($results, $this->component->softDeletes);
        }

        $this->applySummaries($results);

        $sortField = $this->makeSortField($this->component->sortField);

        $results = $this->component->multiSort
            ? $this->applyMultipleSort($results)
            : $this->applySingleSort($results, $sortField);

        $results = $this
            ->applyPerPage($results);

        $this->setTotalCount($results);

        if (filled(data_get($this->component, 'setUp.lazy'))) {
            return $results;
        }

        $collection = $results->getCollection();

        return $results->setCollection($this->transform($collection, $this->component)); // @phpstan-ignore-line
    }
}
