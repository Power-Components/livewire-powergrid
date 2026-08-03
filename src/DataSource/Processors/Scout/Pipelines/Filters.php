<?php

namespace PowerComponents\LivewirePowerGrid\DataSource\Processors\Scout\Pipelines;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Builder as ScoutBuilder;
use PowerComponents\LivewirePowerGrid\DataSource\Support\FilterNormalizer;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class Filters
{
    public function __construct(protected PowerGridComponent $component) {}

    /** @param  ScoutBuilder<Model>  $builder
     * @return ScoutBuilder<Model> */
    public function handle(ScoutBuilder $builder, Closure $next): ScoutBuilder
    {
        $filterDefinitions = collect($this->component->filters());

        if ($filterDefinitions->isEmpty() || empty($this->component->filters)) {
            return $next($builder);
        }

        foreach ($this->component->filters as $columns) {
            foreach (FilterNormalizer::normalize((array) $columns) as $field => $value) {
                $hasDefinition = $filterDefinitions->contains(
                    fn ($filter) => data_get($filter, 'field') === $field
                );

                if (! $hasDefinition) {
                    continue;
                }

                $builder->where($field, $value);
            }
        }

        return $next($builder);
    }
}
