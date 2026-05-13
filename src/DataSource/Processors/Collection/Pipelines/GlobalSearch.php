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

        $searchableColumns = collect($this->component->columns())
            ->filter(fn (Column|stdClass|array $column) => (bool) data_get($column, 'searchable'));

        if ($searchableColumns->isEmpty()) {
            return $next($collection);
        }

        $results = $collection->filter(function ($row) use ($searchableColumns) {
            $row = (object) $row;

            return $searchableColumns->contains(function (Column|stdClass|array $column) use ($row) {
                $field = strval(data_get($column, 'dataField', data_get($column, 'field')));
                $value = data_get($row, $field);

                $search = trim(strtolower(htmlspecialchars(strval($this->component->search), ENT_QUOTES | ENT_HTML5, 'UTF-8')));

                $search = $this->getBeforeSearchMethod($field, $search);

                return Str::contains(strtolower(strval($value)), strtolower(strval($search)));
            });
        });

        return $next($results);
    }

    protected function getBeforeSearchMethod(string $field, ?string $search): ?string
    {
        $method = 'beforeSearch'.str($field)->headline()->replace(' ', '');

        if (method_exists($this->component, $method)) {
            return strval($this->component->$method($search));
        }

        if (method_exists($this->component, 'beforeSearch')) {
            return strval($this->component->beforeSearch($field, $search));
        }

        return $search;
    }
}
