<?php

namespace PowerComponents\LivewirePowerGrid\Components\Filters;

/** @codeCoverageIgnore */
class FilterDatePicker extends FilterBase
{
    public string $key = 'date';

    /** @var array<string, mixed> */
    public array $params = [];

    /** @param  array<string, mixed>  $params */
    public function params(array $params): FilterDatePicker
    {
        $this->params = $params;

        return $this;
    }
}
