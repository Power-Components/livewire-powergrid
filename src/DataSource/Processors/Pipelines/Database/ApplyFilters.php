<?php

namespace PowerComponents\LivewirePowerGrid\DataSource\Processors\Pipelines\Database;

use Closure;
use PowerComponents\LivewirePowerGrid\DataSource\Processors\Handlers\{FilterHandler, GlobalSearchHandler};
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

class ApplyFilters
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
