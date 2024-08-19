<?php

namespace PowerComponents\LivewirePowerGrid\DataSource\Processors;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\{Collection, Collection as BaseCollection};
use PowerComponents\LivewirePowerGrid\DataSource\DataSourceProcessorInterface;

class CollectionProcessor extends DataSourceBase implements DataSourceProcessorInterface
{
    public static function match(mixed $key): bool
    {
        return $key instanceof Collection;
    }

    public function process(): LengthAwarePaginator|BaseCollection
    {
        $filters = \PowerComponents\LivewirePowerGrid\DataSource\Collection::make(
            new BaseCollection($this->prepareDataSource()), // @phpstan-ignore-line
            $this->component
        )
            ->filterContains()
            ->filter();

        $results = $this->component->applySorting($filters);

        $this->applySummaries($results);

        $this->component->total = $results->count();

        if ($results->count()) {
            $this->component->filtered = $results->pluck($this->component->primaryKey)->toArray();

            $perPage   = $this->isExport ? $this->component->total : intval(data_get($this->component->setUp, 'footer.perPage'));
            $paginated = \PowerComponents\LivewirePowerGrid\DataSource\Collection::paginate($results, $perPage);

            $results = $paginated->setCollection(
                $this->transform($paginated->getCollection(), $this->component)
            );
        };

        return $results;
    }
}
