<?php

namespace PowerComponents\LivewirePowerGrid\DataSource\Processors\Collection\Pipelines;

use Closure;
use Illuminate\Support\{Collection, Str};
use PowerComponents\LivewirePowerGrid\Contracts\PowerGridContext;

final class GlobalSearch
{
    public function __construct(protected PowerGridContext $component) {}

    /**
     * @param  Collection<int, mixed>  $collection
     * @return Collection<int, mixed>
     */
    public function handle(Collection $collection, Closure $next): Collection
    {
        if (blank($this->component->state()->search)) {
            return $next($collection);
        }

        $searchableColumns = collect($this->component->declaredColumns())
            ->filter(function (mixed $column): bool {
                return (bool) data_get($column, 'searchable');
            });

        if ($searchableColumns->isEmpty()) {
            return $next($collection);
        }

        $results = $collection->filter(function ($row) use ($searchableColumns) {
            $row = (object) $row;

            return $searchableColumns->contains(function (mixed $column) use ($row) {
                /** @var string $field */
                $field = data_get($column, 'dataField', data_get($column, 'field'));
                /** @var string $value */
                $value = data_get($row, $field);

                /** @var string $search */
                $search = trim(strtolower(htmlspecialchars(strval($this->component->state()->search), ENT_QUOTES | ENT_HTML5, 'UTF-8')));

                $search = $this->getBeforeSearchMethod($field, $search);

                return Str::contains(strtolower($value), strtolower(strval($search)));
            });
        });

        return $next($results);
    }

    protected function getBeforeSearchMethod(string $field, ?string $search): ?string
    {
        return $this->component->applyBeforeSearch($field, $search);
    }
}
