<?php

namespace PowerComponents\LivewirePowerGrid\DataSource\Processors\Scout\Pipelines;

use Closure;
use Illuminate\Support\{Str, Stringable};
use Laravel\Scout\Builder as ScoutBuilder;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class Search
{
    public function __construct(protected PowerGridComponent $component) {}

    public function handle(ScoutBuilder $builder, Closure $next): ScoutBuilder
    {
        if (blank($this->component->search)) {
            return $next($builder);
        }

        $builder->query = Str::of($builder->query)
            ->when(
                $this->component->search,
                fn (Stringable $self) => $self->prepend($this->component->search.',')
            )
            ->toString();

        return $next($builder);
    }
}
