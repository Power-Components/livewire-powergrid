<?php

namespace PowerComponents\LivewirePowerGrid\DataSource\Processors\Database\Pipelines;

use Closure;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

class SoftDeletes
{
    public function __construct(protected PowerGridComponent $component) {}

    public function handle(mixed $query, Closure $next): mixed
    {
        if (! ($query instanceof EloquentBuilder || $query instanceof MorphToMany)) {
            return $next($query);
        }

        $softDeletes = data_get($this->component, 'softDeletes');

        if ($softDeletes === 'withTrashed') {
            $query->withTrashed();
        } elseif ($softDeletes === 'onlyTrashed') {
            $query->onlyTrashed();
        }

        return $next($query);
    }
}
