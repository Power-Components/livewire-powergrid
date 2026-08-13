<?php

namespace PowerComponents\LivewirePowerGrid\DataSource\Processors\Database\Pipelines;

use Closure;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use PowerComponents\LivewirePowerGrid\Contracts\PowerGridContext;

class SoftDeletes
{
    public function __construct(protected PowerGridContext $component) {}

    public function handle(mixed $query, Closure $next): mixed
    {
        if (! ($query instanceof EloquentBuilder || $query instanceof MorphToMany)) {
            return $next($query);
        }

        $softDeletes = $this->component->state()->softDeletes;

        if ($query instanceof EloquentBuilder) {
            if ($softDeletes === 'withTrashed') {
                /** @phpstan-ignore method.notFound */
                $query->withTrashed();
            } elseif ($softDeletes === 'onlyTrashed') {
                /** @phpstan-ignore method.notFound */
                $query->onlyTrashed();
            }
        }

        return $next($query);
    }
}
