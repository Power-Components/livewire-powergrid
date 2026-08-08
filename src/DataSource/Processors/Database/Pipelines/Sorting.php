<?php

namespace PowerComponents\LivewirePowerGrid\DataSource\Processors\Database\Pipelines;

use Closure;
use Illuminate\Database\Eloquent\{Builder as EloquentBuilder, Model};
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Query\Builder as QueryBuilder;
use PowerComponents\LivewirePowerGrid\DataSource\Support\Sql;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

class Sorting
{
    public function __construct(protected PowerGridComponent $component) {}

    public function handle(mixed $query, Closure $next): mixed
    {
        if (! ($query instanceof EloquentBuilder || $query instanceof MorphToMany || $query instanceof QueryBuilder)) {
            return $next($query);
        }

        if (filled($this->component->sortField)) {
            if ($this->component->multiSort) {
                // sortArray is mass-assignable; reject any key that is not a
                // declared column instead of forwarding it to ORDER BY.
                $valid = collect($this->component->sortArray)
                    ->keys()
                    ->every(fn (string $field) => $this->component->isValidSortField($field));

                if ($valid) {
                    $this->applyMultipleSort($query);
                }
            } else {
                if ($this->component->isValidSortField($this->component->sortField)) {
                    $this->applySingleSort($query, $this->component->sortField, $this->component->sortDirection);
                }
            }
        }

        return $next($query);
    }

    /** @param  EloquentBuilder<Model>|MorphToMany<Model, Model>|QueryBuilder  $query */
    private function applySingleSort(EloquentBuilder|MorphToMany|QueryBuilder $query, string $sortField, string $direction): void
    {
        // A sort callback may interpolate the direction into a raw ORDER BY
        // (e.g. orderByRaw), so it must be restricted to "asc"/"desc" here.
        $direction = Sql::sanitizeSortDirection($direction);

        $sortCallback = $this->component->getSortCallback($sortField);

        if ($sortCallback !== null) {
            $sortCallback($query, $direction);

            return;
        }

        /** @var 'asc'|'desc' $direction */
        $query->orderBy($this->component->resolveSortField($sortField), $direction);
    }

    /** @param  EloquentBuilder<Model>|MorphToMany<Model, Model>|QueryBuilder  $results */
    private function applyMultipleSort(EloquentBuilder|MorphToMany|QueryBuilder $results): void
    {
        foreach ($this->component->sortArray as $sortField => $direction) {
            $direction = Sql::sanitizeSortDirection($direction);

            $sortCallback = $this->component->getSortCallback($sortField);

            if ($sortCallback !== null) {
                $sortCallback($results, $direction);

                continue;
            }

            /** @var 'asc'|'desc' $direction */
            $results->orderBy($this->component->resolveSortField($sortField), $direction);
        }
    }
}
