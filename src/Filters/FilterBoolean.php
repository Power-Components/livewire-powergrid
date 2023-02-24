<?php

namespace PowerComponents\LivewirePowerGrid\Filters;

class FilterBoolean extends FilterBase
{
    public string $trueLabel = 'Yes';

    public string $falseLabel = 'No';

    public function label(string $trueLabel, string $falseLabel): FilterBoolean
    {
        $this->trueLabel  = $trueLabel;
        $this->falseLabel = $falseLabel;

        return $this;
    }
}
