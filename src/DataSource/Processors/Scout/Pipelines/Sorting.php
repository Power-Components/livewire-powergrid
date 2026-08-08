<?php

namespace PowerComponents\LivewirePowerGrid\DataSource\Processors\Scout\Pipelines;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Builder as ScoutBuilder;
use PowerComponents\LivewirePowerGrid\DataSource\Support\Sql;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class Sorting
{
    public function __construct(protected PowerGridComponent $component) {}

    /** @param  ScoutBuilder<Model>  $builder
     * @return ScoutBuilder<Model> */
    public function handle(ScoutBuilder $builder, Closure $next): ScoutBuilder
    {
        if (blank($this->component->sortField)) {
            return $next($builder);
        }

        if ($this->component->multiSort) {
            foreach ($this->component->sortArray as $sortField => $direction) {
                if (! $this->component->isValidSortField($sortField)) {
                    continue;
                }

                $builder->orderBy($sortField, Sql::sanitizeSortDirection($direction));
            }

            return $next($builder);
        }

        if (! $this->component->isValidSortField($this->component->sortField)) {
            return $next($builder);
        }

        $builder->orderBy(
            $this->component->sortField,
            Sql::sanitizeSortDirection($this->component->sortDirection)
        );

        return $next($builder);
    }
}
