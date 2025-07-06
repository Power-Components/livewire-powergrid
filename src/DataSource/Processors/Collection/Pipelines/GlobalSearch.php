<?php

namespace PowerComponents\LivewirePowerGrid\DataSource\Processors\Collection\Pipelines;

use Closure;
use Illuminate\Support\{Collection, Str};
use PowerComponents\LivewirePowerGrid\{Column, PowerGridComponent};
use stdClass;

final class GlobalSearch
{
    public function __construct(protected PowerGridComponent $component) {}

    public function handle(Collection $collection, Closure $next): Collection
    {
        if (blank($this->component->search)) {
            return $next($collection);
        }

        $search = strtolower($this->component->search);

        $searchableColumns = collect($this->component->columns())
            ->filter(fn (Column|stdClass|array $column) => (bool) data_get($column, 'searchable'));

        if ($searchableColumns->isEmpty()) {
            return $next($collection);
        }

        $results = $collection->filter(function ($row) use ($searchableColumns, $search) {
            $row = (object) $row;

            return $searchableColumns->contains(function (Column|stdClass|array $column) use ($row, $search) {
                $field = $column->dataField ?: $column->field; // @phpstan-ignore-line
                $value = data_get($row, $field);

                return Str::contains(strtolower($value), $search);
            });
        });

        return $next($results);
    }
}
