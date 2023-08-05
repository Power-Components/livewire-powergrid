<?php

namespace PowerComponents\LivewirePowerGrid\Components\Rules\Support;

class SlotRule
{
    public function apply(array $ruleData, array &$output): void
    {
        if ($slot = strval(data_get($ruleData, 'slot'))) {
            $output['slot'] = $slot;
        }
    }
}
