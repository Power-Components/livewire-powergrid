<?php

namespace PowerComponents\LivewirePowerGrid\Components\Rules\Support;

class DisableRule
{
    /** @return array{attributes?: array{disabled: string}} */
    public function apply(bool $ruleData = false): array
    {
        $output = [];

        if ($ruleData) {
            $output['attributes'] = ['disabled' => 'disabled'];
        }

        return $output;
    }
}
