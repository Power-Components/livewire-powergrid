<?php

namespace PowerComponents\LivewirePowerGrid\DataSource\Processors\Pipelines;

use Closure;
use PowerComponents\LivewirePowerGrid\Contracts\PowerGridContext;
use PowerComponents\LivewirePowerGrid\DataSource\Summaries\SummaryCalculator;

class Summaries
{
    public function __construct(protected PowerGridContext $component) {}

    public function handle(mixed $query, Closure $next): mixed
    {
        if (! $this->component->hasSummarizeInColumns()) {
            return $next($query);
        }

        // Compute the raw aggregate values once (single batched query for DB sources,
        // single in-memory pass for collections). The query is forwarded untouched;
        // formatting and column assignment happen at render via hydrateSummaries().
        $this->component->setSummaryValues((new SummaryCalculator($this->component))->compute($query));

        return $next($query);
    }
}
