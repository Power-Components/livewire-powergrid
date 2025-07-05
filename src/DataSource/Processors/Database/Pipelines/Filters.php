<?php

namespace PowerComponents\LivewirePowerGrid\DataSource\Processors\Database\Pipelines;

use Closure;
use PowerComponents\LivewirePowerGrid\DataSource\Processors\Database\Handlers\GlobalSearchHandler;
use PowerComponents\LivewirePowerGrid\DataSource\Processors\Database\Handlers\{FilterHandler};
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

class Filters
{
    public function __construct(protected PowerGridComponent $component)
    {
    }

    public function handle(mixed $query, Closure $next): mixed
    {
        (new GlobalSearchHandler($this->component))->apply($query);
        (new FilterHandler($this->component))->apply($query);

        return $next($query);
    }
}
