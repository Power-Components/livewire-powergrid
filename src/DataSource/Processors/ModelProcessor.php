<?php

namespace PowerComponents\LivewirePowerGrid\DataSource\Processors;

use Illuminate\Contracts\Pagination\LengthAwarePaginator as LengthAwarePaginatorInterface;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Pagination\{LengthAwarePaginator, Paginator};
use Illuminate\Support\Collection as BaseCollection;
use PowerComponents\LivewirePowerGrid\DataSource\{Builder, DataSourceProcessorInterface};
use Throwable;

class ModelProcessor extends DataSourceBase implements DataSourceProcessorInterface
{
    public static function match(mixed $key): bool
    {
        return true;
    }

    /**
     * @throws Throwable
     */
    public function process(): BaseCollection|LengthAwarePaginator|LengthAwarePaginatorInterface|Paginator|MorphToMany
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

        $this->applyColumnRawQueries($results);

        $this->applySummaries($results);

        if (filled($this->component->sortField)) {
            $results = $this->component->multiSort
                ? $this->applyMultipleSort($results)
                : $results->orderBy($this->makeSortField($this->component->sortField), $this->component->sortDirection);
        }

        $results = $this->applyPerPage($results);

        $this->setTotalCount($results);

        if (filled(data_get($this->component, 'setUp.lazy'))) {
            return $results;
        }

        $collection = $results->getCollection(); // @phpstan-ignore-line

        return $results->setCollection($this->transform($collection, $this->component)); // @phpstan-ignore-line
    }

    private function applyColumnRawQueries(MorphToMany|EloquentBuilder|QueryBuilder $results): void
    {
        collect($this->component->columns())
            ->filter(fn ($column) => filled(data_get($column, 'rawQueries')))
            ->map(function ($column) use ($results) {
                foreach ((array) data_get($column, 'rawQueries', []) as $rawQuery) {
                    /** @var array $rawQuery */
                    $method   = $rawQuery['method'];
                    $sql      = $rawQuery['sql'];
                    $bindings = $rawQuery['bindings'];
                    $enabled  = false;

                    if (isset($rawQuery['enabled'])) {
                        $enabled = $rawQuery['enabled'];
                        $enabled = is_callable($enabled) ? $enabled($this->component) : $enabled;
                    }

                    $bindings = array_map(function ($param) {
                        if ($param instanceof \Closure) {
                            $param = $param($this->component);
                        } else {
                            $param = preg_replace_callback('/\{(\w+)\}/', function ($matches) {
                                $property = trim($matches[1]);

                                return $this->component->{$property};
                            }, $param);
                        }

                        return $param;
                    }, $bindings);

                    $sql = preg_replace_callback('/\{(\w+)\}/', function ($matches) {
                        $property = trim($matches[1]);

                        return $this->component->{$property};
                    }, $sql);

                    if ($sql && $enabled) {
                        $results->{$method}($sql, $bindings);
                    }

                    return $rawQuery;
                }
            });
    }
}
