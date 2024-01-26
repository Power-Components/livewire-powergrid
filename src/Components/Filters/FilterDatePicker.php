<?php

namespace PowerComponents\LivewirePowerGrid\Components\Filters;

/** @codeCoverageIgnore */
class FilterDatePicker extends FilterBase
{
    public string $key = 'date';

    public array $params = [];

    public function params(array $params): FilterDatePicker
    {
        $this->params = $params;

        return $this;
    }
}
