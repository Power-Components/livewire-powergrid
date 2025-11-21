<?php

namespace PowerComponents\LivewirePowerGrid\DataSource\Processors;

use Illuminate\Pipeline\Pipeline;
use PowerComponents\LivewirePowerGrid\DataSource\DataTransformer;
use PowerComponents\LivewirePowerGrid\DataSource\Processors\Database\Pipelines;
use PowerComponents\LivewirePowerGrid\DataSource\Processors\Pipelines as CommonPipelines;

class ModelProcessor extends DataSourceBase
{
    public static function match(mixed $key): bool
    {
        return true;
    }

    public function process(array $properties = []): array
    {
        $datasource = $this->component->datasource($properties);

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

        $paginate = app(Pipeline::class)
            ->send($query)
            ->through([
                new CommonPipelines\Pagination($this->component),
            ])
            ->thenReturn();

        /** @var \Illuminate\Support\Collection $collection */
        $collection = $paginate->getCollection();

        if (filled(data_get($this->component, 'setUp.lazy'))) {
            $paginate->setCollection($collection);

            return [
                'results' => $paginate,
                'transformTime' => 0,
                'actionsByRow' => [],
            ];
        }

        $dataTransformer = new DataTransformer($this->component);
        $transformResult = $dataTransformer->transform($collection);

        return [
            'results' => $paginate->setCollection($transformResult->getCollection()),
            'transformTime' => $transformResult->getTransformTimeInMs(),
            'actionsByRow' => $transformResult->getActionsByRow(),
        ];
    }
}
