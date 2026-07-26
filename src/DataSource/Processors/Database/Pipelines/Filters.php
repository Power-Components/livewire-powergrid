<?php

namespace PowerComponents\LivewirePowerGrid\DataSource\Processors\Database\Pipelines;

use Closure;
use Illuminate\Database\Eloquent\{Builder as EloquentBuilder, Model};
use Illuminate\Database\Query\Builder as QueryBuilder;
use PowerComponents\LivewirePowerGrid\DataSource\Processors\Database\Handlers\{FilterHandler, SearchHandlerContract};
use PowerComponents\LivewirePowerGrid\Plugins\FilterBuilder\FilterBuilderHandler;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

class Filters
{
    public function __construct(protected PowerGridComponent $component) {}

    public function handle(mixed $query, Closure $next): mixed
    {
        /** @var EloquentBuilder<Model>|QueryBuilder $query */
        app()->makeWith(SearchHandlerContract::class, [
            'component' => $this->component,
        ])->apply($query);
        (new FilterHandler($this->component))->apply($query);

        $filterBuilder = new FilterBuilderHandler($this->component);

        if ($filterBuilder->isActive()) {
            $filterBuilder->apply($query);
        }

        return $next($query);
    }
}
