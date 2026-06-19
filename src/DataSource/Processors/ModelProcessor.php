<?php

namespace PowerComponents\LivewirePowerGrid\DataSource\Processors;

use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Collection;
use PowerComponents\LivewirePowerGrid\DataSource\DataTransformer;
use PowerComponents\LivewirePowerGrid\DataSource\Processors\Database\Pipelines;
use PowerComponents\LivewirePowerGrid\DataSource\Processors\Pipelines as CommonPipelines;

class ModelProcessor extends DataSourceBase
{
    public static function match(mixed $key): bool
    {
        return true;
    }

    /**
     * @param  array<string, mixed>  $properties
     * @return array{results: mixed, transformTime: float, actionsByRow: array<int|string, list<array<string, mixed>>>}
     */
    public function process(array $properties = [], mixed $datasource = null): array
    {
        $datasource = $datasource ?? $this->component->datasource($properties);

        $this->setCurrentTable($datasource);

        $query = app(Pipeline::class)
            ->send($datasource)
            ->through([
                new Pipelines\Filters($this->component),
                new Pipelines\SoftDeletes($this->component),
                new Pipelines\ColumnRawQueries($this->component),
                new CommonPipelines\Summaries($this->component),
                new Pipelines\Sorting($this->component),
            ])
            ->thenReturn();

        $query = $this->component->transformQuery($query);

        $paginate = app(Pipeline::class)
            ->send($query)
            ->through([
                new CommonPipelines\Pagination($this->component),
            ])
            ->thenReturn();

        /** @var Collection $collection */
        $collection = $paginate->getCollection();

        $dataTransformer = new DataTransformer($this->component);
        $transformResult = $dataTransformer->transform($collection);

        return [
            'results' => $paginate->setCollection($transformResult->getCollection()),
            'transformTime' => $transformResult->getTransformTimeInMs(),
            'actionsByRow' => $transformResult->getActionsByRow(),
        ];
    }
}
