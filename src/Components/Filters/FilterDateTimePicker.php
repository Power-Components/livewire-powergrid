<?php

namespace PowerComponents\LivewirePowerGrid\Components\Filters;

/** @codeCoverageIgnore */
class FilterDateTimePicker extends FilterBase
{
    public string $key = 'datetime';

    /** @var array<string, mixed> */
    public array $params = [
        'enableTime' => true,
    ];

    /** @param  array<string, mixed>  $params */
    public function params(array $params): FilterDateTimePicker
    {
        $this->params = array_merge($this->params, $params);

        return $this;
    }
}
