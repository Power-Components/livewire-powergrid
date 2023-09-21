<?php

namespace PowerComponents\LivewirePowerGrid\Components\Rules\Support;

class SlotRule
{
    public function apply(array $ruleData): array
    {
        $output = [];

        if ($slot = strval(data_get($ruleData, 'slot'))) {
            $output['slot'] = $slot;
        }

        return $output;
    }
}
