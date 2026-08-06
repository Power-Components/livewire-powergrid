<?php

namespace PowerComponents\LivewirePowerGrid\DataSource\Processors\Pipelines;

use Closure;
use Illuminate\Pagination\{LengthAwarePaginator, Paginator};
use Laravel\Scout\Builder as ScoutBuilder;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

class Pagination
{
    public function __construct(protected PowerGridComponent $component) {}

    /** @return LengthAwarePaginator<int, mixed>|Paginator<int, mixed> */
    public function handle(mixed $query, Closure $next): LengthAwarePaginator|Paginator
    {
        /** @var string $pageName */
        $pageName = data_get($this->component->setUp, 'footer.pageName', 'page');
        /** @var int $perPageFromSetup */
        $perPageFromSetup = data_get($this->component->setUp, 'footer.perPage');
        $perPage = $this->clampPerPage(intval($perPageFromSetup));
        /** @var string $recordCount */
        $recordCount = data_get($this->component->setUp, 'footer.recordCount');

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

        return $query->$paginate($this->clampPerPage($count ?: 10), pageName: $pageName);
    }

    private function clampPerPage(int $perPage): int
    {
        $configured = config('livewire-powergrid.max_per_page', 1000);
        $max = is_numeric($configured) ? (int) $configured : 0;

        if ($max > 0 && $perPage > $max) {
            return $max;
        }

        return $perPage;
    }
}
