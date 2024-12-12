<?php

namespace PowerComponents\LivewirePowerGrid\Components\Filters;

/** @codeCoverageIgnore */
class FilterDateTimePicker extends FilterBase
{
    public string $key = 'datetime';

    public array $params = [
        'enableTime' => true,
    ];

    public function params(array $params): FilterDateTimePicker
    {
        $this->params = array_merge($this->params, $params);

        return $this;
    }
}
