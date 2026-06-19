<?php

namespace PowerComponents\LivewirePowerGrid\Components\Rules\Support;

class SlotRule
{
    /** @return array{slot: string} */
    public function apply(string $ruleData): array
    {
        $output = [];

        $output['slot'] = $ruleData;

        return $output;
    }
}
