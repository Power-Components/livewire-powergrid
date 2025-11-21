<?php

namespace PowerComponents\LivewirePowerGrid\DataSource;

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
    ) {}

    public static function make(PowerGridComponent $powerGridComponent, array $properties = []): ProcessDataSource
    {
        return new self($powerGridComponent, $properties);
    }

    /**
     * @throws Throwable
     */
    public function get(bool $isExport = false): array
    {
        $processors = [
            CollectionProcessor::class,
            ScoutBuilderProcessor::class,
        ];

        foreach ($processors as $processor) {
            // @phpstan-ignore-next-line
            if ($processor::match($this->component->datasource($this->properties))) {
                $instance = new $processor($this->component, $isExport);

                return $instance->process($this->properties);
            }
        }

        return (new ModelProcessor($this->component, $isExport))->process($this->properties);
    }
}
