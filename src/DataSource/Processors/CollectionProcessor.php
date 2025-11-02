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

    public function process(array $properties = []): array
    {
        $datasource = $this->component->datasource($properties);

        $collection = new BaseCollection($datasource);

        /** @var BaseCollection $results */
        $results = app(Pipeline::class)
            ->send($collection)
            ->through([
                new Pipelines\GlobalSearch($this->component),
                new Pipelines\Filters($this->component),
                new Pipelines\Sorting($this->component),
                new CommonPipelines\Summaries($this->component),
            ])
            ->thenReturn();

        $paginated = $results;
        $dataTransformer = new DataTransformer($this->component);
        $actionsByRow = [];
        $timeInMs = 0;

        if ($results->count() > 0) {
            $this->component->filtered = $results->pluck($this->component->primaryKey)->toArray();
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

    private function paginate(BaseCollection $results): LengthAwarePaginator
    {
        $perPage = $this->isExport
            ? $results->count()
            : intval(data_get($this->component->setUp, 'footer.perPage', 10));

        $perPage = $perPage > 0 ? $perPage : $results->count();
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
}
