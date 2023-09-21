<?php

namespace PowerComponents\LivewirePowerGrid\Components\Rules\Support;

class DisableRule
{
    public function apply(array $ruleData, array &$output): void
    {
        if (boolval(data_get($ruleData, 'disable', false))) {
            $output['attributes'] = ['disabled' => 'disabled'];
        }
    }
}
