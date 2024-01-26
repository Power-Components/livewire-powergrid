<?php

namespace PowerComponents\LivewirePowerGrid\Components\Filters;

/** @codeCoverageIgnore */
class FilterDateTimePicker extends FilterBase
{
    public string $key = 'date_time';

    public array $params = [];

    public function params(array $params): FilterDateTimePicker
    {
        $this->params = $params;

        return $this;
    }
}
