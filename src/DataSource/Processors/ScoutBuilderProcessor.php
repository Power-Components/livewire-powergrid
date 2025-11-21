<?php

namespace PowerComponents\LivewirePowerGrid\DataSource\Processors;

use Illuminate\Pipeline\Pipeline;
use Laravel\Scout\Builder as ScoutBuilder;
use PowerComponents\LivewirePowerGrid\DataSource\DataTransformer;
use PowerComponents\LivewirePowerGrid\DataSource\Processors\Pipelines as CommonPipelines;
use PowerComponents\LivewirePowerGrid\DataSource\Processors\Scout\Pipelines;

class ScoutBuilderProcessor extends DataSourceBase
{
    public static function match(mixed $key): bool
    {
        return $key instanceof ScoutBuilder;
    }

    public function process(array $properties = []): array
    {
        /** @var ScoutBuilder $datasource */
        $datasource = $this->component->datasource($properties);

        /** @var ScoutBuilder $query */
        $query = app(Pipeline::class)
            ->send($datasource)
            ->through([
                new Pipelines\Search($this->component),
                new Pipelines\Filters($this->component),
                new Pipelines\Sorting($this->component),
            ])
            ->thenReturn();

        $paginate = app(Pipeline::class)
            ->send($query)
            ->through([
                new CommonPipelines\Pagination($this->component),
            ])
            ->thenReturn();

        $dataTransformer = new DataTransformer($this->component);
        $transformResult = $dataTransformer->transform($paginate->getCollection());

        $paginate->setCollection($transformResult->collection);

        return [
            'results' => $paginate,
            'transformTime' => $transformResult->transformTimeInMs,
        ];
    }
}
