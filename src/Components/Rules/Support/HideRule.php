<?php

namespace PowerComponents\LivewirePowerGrid\Components\Rules\Support;

class HideRule
{
    public function apply(array $ruleData): array
    {
        $output = [];

        if (boolval(data_get($ruleData, 'hide', false))) {
            $output['hide'] = true;
        }

        return $output;
    }
}
