<?php

namespace PowerComponents\LivewirePowerGrid\DataSource\Processors\Scout\Pipelines;

use Closure;
use Laravel\Scout\Builder as ScoutBuilder;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class Filters
{
    public function __construct(protected PowerGridComponent $component) {}

    public function handle(ScoutBuilder $builder, Closure $next): ScoutBuilder
    {
        if (empty($this->component->filters)) {
            return $next($builder);
        }

        collect($this->component->filters)
            ->each(
                fn (array $filters) => collect($filters)
                    ->each(fn (string $value, string $field) => $builder->where($field, $value))
            );

        return $next($builder);
    }
}
