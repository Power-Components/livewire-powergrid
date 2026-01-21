<?php

namespace PowerComponents\LivewirePowerGrid\DataSource\Processors\Database\Pipelines;

use Closure;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Str;
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
                $this->applyMultipleSort($query);
            } else {
                $this->applySingleSort($query, $this->component->sortField, $this->component->sortDirection);
            }
        }

        return $next($query);
    }

    private function applySingleSort(EloquentBuilder|MorphToMany|QueryBuilder $query, string $sortField, string $direction): void
    {
        $sortCallback = $this->component->getSortCallback($sortField);

        if ($sortCallback !== null) {
            $sortCallback($query, $direction);

            return;
        }

        $query->orderBy($this->makeSortField($sortField), $direction);
    }

    private function applyMultipleSort(EloquentBuilder|MorphToMany|QueryBuilder $results): void
    {
        foreach ($this->component->sortArray as $sortField => $direction) {
            $sortCallback = $this->component->getSortCallback($sortField);

            if ($sortCallback !== null) {
                $sortCallback($results, $direction);

                continue;
            }

            $results->orderBy($this->makeSortField($sortField), $direction);
        }
    }

    private function makeSortField(string $sortField): string
    {
        if (Str::of($sortField)->contains('.') || $this->component->ignoreTablePrefix) {
            return $sortField;
        }

        return $this->component->currentTable.'.'.$sortField;
    }
}
