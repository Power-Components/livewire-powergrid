<?php

namespace PowerComponents\LivewirePowerGrid;

use PowerComponents\LivewirePowerGrid\DataSource\{DataSourceProcessorInterface,
    Processors\CollectionProcessor,
    Processors\ModelProcessor,
    Processors\ScoutBuilderProcessor
};
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection as BaseCollection;

class ProcessDataSource
{
    public function __construct(
        public PowerGridComponent $component,
        public array $properties = [],
    ) {
    }

    public static function fillData(PowerGridComponent $powerGridComponent, array $properties = []): ProcessDataSource
    {
        return new self($powerGridComponent, $properties);
    }

    public function get(bool $isExport = false): BaseCollection|LengthAwarePaginator|\Illuminate\Contracts\Pagination\LengthAwarePaginator|Paginator|MorphToMany
    {
        $processors = [
            CollectionProcessor::class,
            ScoutBuilderProcessor::class,
        ];

        foreach ($processors as $processor) {
            /** @var DataSourceProcessorInterface $processor */
            if ($processor::match($this->component->datasource($this->properties ?? null))) {
                $instance = new $processor($this->component, $isExport);

                return $instance->process(); // @phpstan-ignore-line
            }
        }

        return (new ModelProcessor($this->component, $isExport))->process();
    }
}
