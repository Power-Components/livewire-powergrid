<?php

namespace PowerComponents\LivewirePowerGrid\DataSource\Processors\Scout\Pipelines;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Builder as ScoutBuilder;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class Filters
{
    public function __construct(protected PowerGridComponent $component) {}

    /** @param  ScoutBuilder<Model>  $builder
     * @return ScoutBuilder<Model> */
    public function handle(ScoutBuilder $builder, Closure $next): ScoutBuilder
    {
        if (empty($this->component->filters)) {
            return $next($builder);
        }

        collect($this->component->filters)
            ->each(function (mixed $filters) use ($builder) {
                collect($filters)->each(function (mixed $value, mixed $field) use ($builder) {
                    /** @var string $field */
                    /** @var string $value */
                    $builder->where($field, $value);
                });
            });

        return $next($builder);
    }
}
