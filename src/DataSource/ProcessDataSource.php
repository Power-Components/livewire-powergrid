<?php

namespace PowerComponents\LivewirePowerGrid\DataSource;

use PowerComponents\LivewirePowerGrid\DataSource\{Processors\CollectionProcessor,
    Processors\DataSourceBase,
    Processors\ModelProcessor,
    Processors\ScoutBuilderProcessor};
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use Throwable;

class ProcessDataSource
{
    public mixed $datasource = null;

    /** @param  array<string, mixed>  $properties */
    public function __construct(
        public PowerGridComponent $component,
        public array $properties = [],
    ) {}

    /** @param  array<string, mixed>  $properties */
    public static function make(PowerGridComponent $powerGridComponent, array $properties = []): ProcessDataSource
    {
        return new self($powerGridComponent, $properties);
    }

    /**
     * @return array{results: mixed, transformTime: float, actionsByRow?: array<int|string, list<array<string, mixed>>>}
     *
     * @throws Throwable
     */
    public function get(bool $isExport = false): array
    {
        if (is_null($this->datasource)) {
            $this->datasource = $this->component->datasource($this->properties);
        }

        $datasource = is_object($this->datasource) ? clone $this->datasource : $this->datasource;

        /** @var list<class-string<DataSourceBase>> $processors */
        $processors = [
            CollectionProcessor::class,
            ScoutBuilderProcessor::class,
        ];

        foreach ($processors as $processor) {
            if ($processor::match($datasource)) {
                $instance = new $processor($this->component, $isExport);

                return $instance->process($this->properties, $datasource);
            }
        }

        return (new ModelProcessor($this->component, $isExport))->process($this->properties, $datasource);
    }
}
