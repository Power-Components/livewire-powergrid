<?php

namespace PowerComponents\LivewirePowerGrid\DataSource\Processors\Database\Pipelines;

use Closure;
use PowerComponents\LivewirePowerGrid\DataSource\Processors\Database\Handlers\{FilterHandler, SearchHandlerContract};
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

class Filters
{
    public function __construct(protected PowerGridComponent $component) {}

    public function handle(mixed $query, Closure $next): mixed
    {
        app()->makeWith(SearchHandlerContract::class, [
            'component' => $this->component,
        ])->apply($query);
        (new FilterHandler($this->component))->apply($query);

        return $next($query);
    }
}
