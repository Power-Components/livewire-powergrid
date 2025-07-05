<?php

namespace PowerComponents\LivewirePowerGrid\DataSource\Processors;

use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Collection as BaseCollection;
use PowerComponents\LivewirePowerGrid\DataSource\DataTransformer;
use PowerComponents\LivewirePowerGrid\DataSource\Processors\Database\Pipelines;
use PowerComponents\LivewirePowerGrid\DataSource\Processors\Pipelines as CommonPipelines;

class ModelProcessor extends DataSourceBase
{
    public static function match(mixed $key): bool
    {
        return true;
    }

    public function process(): array
    {
        $this->setCurrentTable($this->prepareDataSource());

        $query = app(Pipeline::class)
            ->send($this->prepareDataSource())
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

        $this->setTotalCount($paginate);

        if (filled(data_get($this->component, 'setUp.lazy'))) {
            $count = data_get($this->component, 'setUp.lazy.rowsPerChildren');

            return [
                'results' => $paginate->setCollection(
                    $paginate->getCollection()->take($count)
                ),
                'transformTime' => 0,
            ];
        }

        /** @var BaseCollection $collection */
        $collection = $paginate->getCollection();

        $dataTransformer = new DataTransformer($this->component);

        $transformResult = $dataTransformer->transform($collection);

        return [
            'results'       => $paginate->setCollection($transformResult->collection),
            'transformTime' => $transformResult->transformTimeInMs,
        ];
    }
}
