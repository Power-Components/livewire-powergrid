<?php

namespace PowerComponents\LivewirePowerGrid\Components\Rules\Support;

class DisableRule
{
    public function apply(bool $ruleData = false): array
    {
        $output = [];

        if ($ruleData) {
            $output['attributes'] = ['disabled' => 'disabled'];
        }

        return $output;
    }
}
