<?php

namespace PowerComponents\LivewirePowerGrid\DataSource\Processors\Pipelines;

use Closure;
use Illuminate\Pagination\{LengthAwarePaginator, Paginator};
use Laravel\Scout\Builder as ScoutBuilder;
use PowerComponents\LivewirePowerGrid\Contracts\{GridConfig, PowerGridContext};

class Pagination
{
    public function __construct(protected PowerGridContext $component) {}

    /** @return LengthAwarePaginator<int, mixed>|Paginator<int, mixed> */
    public function handle(mixed $query, Closure $next): LengthAwarePaginator|Paginator
    {
        $setUp = $this->component->state()->setUp;
        $paginateRaw = $this->component->state()->paginateRaw;

        /** @var string $pageName */
        $pageName = data_get($setUp, 'footer.pageName', 'page');
        /** @var int $perPageFromSetup */
        $perPageFromSetup = data_get($setUp, 'footer.perPage');
        $perPage = $this->clampPerPage(intval($perPageFromSetup));
        /** @var string $recordCount */
        $recordCount = data_get($setUp, 'footer.recordCount');

        if ($query instanceof ScoutBuilder) {
            $paginate = match (true) {
                $recordCount == 'min' => 'simplePaginate',
                ($paginateRaw && $recordCount == 'min') => 'simplePaginateRaw', // @phpstan-ignore-line
                $paginateRaw => 'paginateRaw',
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

        $this->component->resetToFirstPage($pageName);

        return $query->$paginate($this->clampPerPage($count ?: 10), pageName: $pageName);
    }

    private function clampPerPage(int $perPage): int
    {
        $configured = app(GridConfig::class)->get('livewire-powergrid.max_per_page', 1000);
        $max = is_numeric($configured) ? (int) $configured : 0;

        if ($max > 0 && $perPage > $max) {
            return $max;
        }

        return $perPage;
    }
}
