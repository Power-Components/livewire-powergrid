<?php

namespace PowerComponents\LivewirePowerGrid\DataSource\Processors\Pipelines;

use Closure;
use Illuminate\Pagination\{LengthAwarePaginator, Paginator};
use Laravel\Scout\Builder as ScoutBuilder;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

class Pagination
{
    public function __construct(protected PowerGridComponent $component) {}

    public function handle(mixed $query, Closure $next): LengthAwarePaginator|Paginator
    {
        $pageName = strval(data_get($this->component->setUp, 'footer.pageName', 'page'));
        $perPage = intval(data_get($this->component->setUp, 'footer.perPage'));
        $recordCount = strval(data_get($this->component->setUp, 'footer.recordCount'));

        if ($query instanceof ScoutBuilder) {
            $paginate = match (true) {
                $recordCount == 'min' => 'simplePaginate',
                ($this->component->paginateRaw && $recordCount == 'min') => 'simplePaginateRaw', // @phpstan-ignore-line
                $this->component->paginateRaw => 'paginateRaw',
                default => 'paginateSafe',
            };
        } else {
            $paginate = match (true) {
                $recordCount === 'min' => 'simplePaginate',
                default => 'paginate',
            };
        }

        if ($perPage > 0) {
            return $query->$paginate($perPage, pageName: $pageName);
        }

        $count = $query->count(); // @phpstan-ignore-line

        $this->component->gotoPage(1, pageName: $pageName);

        return $query->$paginate($count ?: 10, pageName: $pageName);
    }
}
