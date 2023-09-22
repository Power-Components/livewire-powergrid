<?php

namespace PowerComponents\LivewirePowerGrid\Components\Rules\Support;

class SlotRule
{
    public function apply(string $ruleData): array
    {
        $output = [];

        $output['slot'] = $ruleData;

        return $output;
    }
}
