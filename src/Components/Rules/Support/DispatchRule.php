<?php

namespace PowerComponents\LivewirePowerGrid\Components\Rules\Support;

use Illuminate\Support\Js;

class DispatchRule
{
    public function apply(array $ruleData): array
    {
        $output = [];

        $event  = strval(data_get($ruleData, 'event'));
        $params = (array) data_get($ruleData, 'params', []);

        $output['attributes'] = ['wire:click' => "\$dispatch('{$event}', " . Js::from($params) . ")"];

        return $output;
    }
}
