<?php

namespace PowerComponents\LivewirePowerGrid\DataSource\Processors\Scout\Pipelines;

use Closure;
use Laravel\Scout\Builder as ScoutBuilder;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class Sorting
{
    public function __construct(protected PowerGridComponent $component) {}

    public function handle(ScoutBuilder $builder, Closure $next): ScoutBuilder
    {
        if (blank($this->component->sortField)) {
            return $next($builder);
        }

        if ($this->component->multiSort) {
            foreach ($this->component->sortArray as $sortField => $direction) {
                $builder->orderBy($sortField, $direction);
            }

            return $next($builder);
        }

        $builder->orderBy($this->component->sortField, $this->component->sortDirection);

        return $next($builder);
    }
}
