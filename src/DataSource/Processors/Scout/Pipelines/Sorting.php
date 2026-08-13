<?php

namespace PowerComponents\LivewirePowerGrid\DataSource\Processors\Scout\Pipelines;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Builder as ScoutBuilder;
use PowerComponents\LivewirePowerGrid\Contracts\PowerGridContext;
use PowerComponents\LivewirePowerGrid\DataSource\Support\Sql;

final class Sorting
{
    public function __construct(protected PowerGridContext $component) {}

    /** @param  ScoutBuilder<Model>  $builder
     * @return ScoutBuilder<Model> */
    public function handle(ScoutBuilder $builder, Closure $next): ScoutBuilder
    {
        $state = $this->component->state();

        if (blank($state->sortField)) {
            return $next($builder);
        }

        if ($state->multiSort) {
            foreach ($state->sortArray as $sortField => $direction) {
                if (! $this->component->isValidSortField($sortField)) {
                    continue;
                }

                $builder->orderBy($sortField, Sql::sanitizeSortDirection($direction));
            }

            return $next($builder);
        }

        if (! $this->component->isValidSortField($state->sortField)) {
            return $next($builder);
        }

        $builder->orderBy(
            $state->sortField,
            Sql::sanitizeSortDirection($state->sortDirection)
        );

        return $next($builder);
    }
}
