<?php

namespace PowerComponents\LivewirePowerGrid\Filters;

class FilterNumber extends FilterBase
{
    public string $thousands = '';

    public string $decimal = '';

    public function thousands(string $thousands): FilterNumber
    {
        $this->thousands = $thousands;

        return $this;
    }

    public function decimal(string $decimal): FilterNumber
    {
        $this->decimal = $decimal;

        return $this;
    }
}
