<?php

namespace PowerComponents\LivewirePowerGrid\Concerns;

trait Summarize
{
    public bool $headerTotalColumn = false;

    public bool $footerTotalColumn = false;

    private function resolveTotalRow(): void
    {
        collect($this->columns)
            ->each(function ($column) {
                $hasHeader = false;
                $hasFooter = false;

                foreach (['sum', 'count', 'min', 'avg', 'max'] as $operation) {
                    $hasHeader = $hasHeader || data_get($column, "$operation.header");
                    $hasFooter = $hasFooter || data_get($column, "$operation.footer");
                }

                $this->headerTotalColumn = $this->headerTotalColumn || $hasHeader;
                $this->footerTotalColumn = $this->footerTotalColumn || $hasFooter;
            });
    }

    public function hasSummarizeInColumns(): bool
    {
        return collect($this->columns)->contains(function ($column) {
            $operations = ['sum', 'count', 'min', 'avg', 'max'];

            foreach ($operations as $operation) {
                if (data_get($column, "$operation.header")) {
                    return true;
                }

                if (data_get($column, "$operation.footer")) {
                    return true;
                }
            }

            return false;
        });
    }
}
