<?php

namespace PowerComponents\LivewirePowerGrid\Concerns;

use PowerComponents\LivewirePowerGrid\Column;
use stdClass;

trait Summarize
{
    public bool $headerTotalColumn = false;

    public bool $footerTotalColumn = false;

    private function resolveSummarizeColumn(): void
    {
        collect($this->columns)
            ->each(function ($column) {
                $hasHeader = false;
                $hasFooter = false;

                foreach (['sum', 'count', 'min', 'avg', 'max'] as $operation) {
                    $hasHeader = $hasHeader || data_get($column, "properties.summarize.$operation.header");
                    $hasFooter = $hasFooter || data_get($column, "properties.summarize.$operation.footer");
                }

                $this->headerTotalColumn = $this->headerTotalColumn || $hasHeader;
                $this->footerTotalColumn = $this->footerTotalColumn || $hasFooter;
            });
    }

    public function hasSummarizeInColumns(): bool
    {
        return collect($this->columns)
            ->filter(function (array|stdClass|Column $column) { // @phpstan-ignore-line
                return data_get($column, 'properties.summarize');
            })->count() > 0;
    }
}
