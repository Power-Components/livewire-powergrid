<?php

namespace PowerComponents\LivewirePowerGrid\Components\Rules\Support;

class HideRule
{
    public function apply(bool $ruleData = false): array
    {
        $output = [];

        if ($ruleData) {
            $output['hide'] = true;
        }

        return $output;
    }
}
