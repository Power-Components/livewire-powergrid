<?php

namespace PowerComponents\LivewirePowerGrid\DataSource;

use Illuminate\Contracts\Pagination\LengthAwarePaginator as LengthAwarePaginatorInterface;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Pagination\{LengthAwarePaginator, Paginator};
use Illuminate\Support\Collection as BaseCollection;
use PowerComponents\LivewirePowerGrid\DataSource\{Processors\CollectionProcessor,
    Processors\ModelProcessor,
    Processors\ScoutBuilderProcessor};
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use Throwable;

class ProcessDataSource
{
    public function __construct(
        public PowerGridComponent $component,
        public array $properties = [],
    ) {
    }

    public static function make(PowerGridComponent $powerGridComponent, array $properties = []): ProcessDataSource
    {
        return new self($powerGridComponent, $properties);
    }

    /**
     * @throws Throwable
     */
    public function get(bool $isExport = false): BaseCollection|LengthAwarePaginator|LengthAwarePaginatorInterface|Paginator|MorphToMany
    {
        $processors = [
            CollectionProcessor::class,
            ScoutBuilderProcessor::class,
        ];

        foreach ($processors as $processor) {
            /** @var DataSourceProcessorInterface $processor */
            if ($processor::match($this->component->datasource($this->properties))) {
                $instance = new $processor($this->component, $isExport);

                return $instance->process(); // @phpstan-ignore-line
            }
        }

        return (new ModelProcessor($this->component, $isExport))->process();
    }
}
