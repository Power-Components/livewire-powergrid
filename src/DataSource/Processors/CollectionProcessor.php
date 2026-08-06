<?php

namespace PowerComponents\LivewirePowerGrid\DataSource\Processors;

use Illuminate\Pagination\{LengthAwarePaginator, Paginator};
use Illuminate\Routing\Pipeline;
use Illuminate\Support\{Collection, Collection as BaseCollection};
use PowerComponents\LivewirePowerGrid\DataSource\DataTransformer;
use PowerComponents\LivewirePowerGrid\DataSource\Processors\Collection\Pipelines;
use PowerComponents\LivewirePowerGrid\DataSource\Processors\Pipelines as CommonPipelines;

class CollectionProcessor extends DataSourceBase
{
    public static function match(mixed $key): bool
    {
        return $key instanceof Collection
            || is_iterable($key);
    }

    /**
     * @param  array<string, mixed>  $properties
     * @return array{results: mixed, transformTime: float, actionsByRow: array<int|string, list<array<string, mixed>>>}
     */
    public function process(array $properties = [], mixed $datasource = null): array
    {
        $datasource = $datasource ?? $this->component->datasource($properties);

        /** @var array<int, mixed>|BaseCollection<int, mixed> $datasource */
        $collection = new BaseCollection($datasource);

        /** @var BaseCollection<int, mixed> $results */
        $results = app(Pipeline::class)
            ->send($collection)
            ->through([
                new Pipelines\GlobalSearch($this->component),
                new Pipelines\Filters($this->component),
                new Pipelines\Sorting($this->component),
                new CommonPipelines\Summaries($this->component),
            ])
            ->thenReturn();

        $results = $this->component->transformQuery($results);

        /** @var BaseCollection<int, mixed> $results */
        $paginated = $results;
        $dataTransformer = new DataTransformer($this->component);
        $actionsByRow = [];
        $timeInMs = 0;

        if ($results->count() > 0) {
            $plucked = $results->pluck($this->component->primaryKey)->values();
            /** @var list<int|string> $filtered */
            $filtered = $plucked->toArray();
            $this->component->filtered = $filtered;
            $paginated = $this->paginate($results);

            $transformResult = $dataTransformer->transform($paginated->getCollection());
            $actionsByRow = $transformResult->getActionsByRow();
            $timeInMs = $transformResult->getTransformTimeInMs();

            $paginated->setCollection($transformResult->getCollection());
        }

        return [
            'results' => $paginated,
            'transformTime' => $timeInMs,
            'actionsByRow' => $actionsByRow,
        ];
    }

    /** @param  BaseCollection<int, mixed>  $results
     * @return LengthAwarePaginator<int, mixed> */
    private function paginate(BaseCollection $results): LengthAwarePaginator
    {
        /** @var int $perPageFromSetup */
        $perPageFromSetup = data_get($this->component->setUp, 'footer.perPage', 10);
        $perPage = $this->isExport
            ? $results->count()
            : $this->clampPerPage(intval($perPageFromSetup));

        $perPage = $perPage > 0
            ? $perPage
            : ($this->isExport ? $results->count() : $this->clampPerPage($results->count()));
        /** @var string $pageName */
        $pageName = data_get($this->component->setUp, 'footer.pageName', 'page');

        $page = Paginator::resolveCurrentPage($pageName);

        return new LengthAwarePaginator(
            items: $results->forPage($page, $perPage),
            total: $results->count(),
            perPage: $perPage,
            currentPage: $page,
            options: [
                'path' => Paginator::resolveCurrentPath(),
                'pageName' => $pageName,
            ]
        );
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
