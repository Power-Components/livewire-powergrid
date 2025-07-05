<?php

namespace PowerComponents\LivewirePowerGrid\DataSource\Processors;

use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Collection as BaseCollection;
use PowerComponents\LivewirePowerGrid\DataSource\{
    DataTransformer,
    Processors\Pipelines\ApplyPagination,
    Processors\Pipelines\Database\ApplyColumnRawQueries,
    Processors\Pipelines\Database\ApplyFilters,
    Processors\Pipelines\Database\ApplySoftDeletes,
    Processors\Pipelines\Database\ApplySorting,
    Processors\Pipelines\Database\ApplySummaries};

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
                new ApplyFilters($this->component),
                new ApplySoftDeletes($this->component),
                new ApplyColumnRawQueries($this->component),
                new ApplySummaries($this->component),
                new ApplySorting($this->component),
            ])
            ->thenReturn();

        $paginate = app(Pipeline::class)
            ->send($query)
            ->through([
                new ApplyPagination($this->component),
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
