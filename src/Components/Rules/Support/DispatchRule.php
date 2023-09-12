<?php

namespace PowerComponents\LivewirePowerGrid\Components\Rules\Support;

use Illuminate\Support\Js;

class DispatchRule
{
    public function apply(array $ruleData, array &$output): void
    {
        if ($ruleDispatch = (array) data_get($ruleData, 'dispatch', [])) {
            $event  = strval(data_get($ruleDispatch, 'event'));
            $params = (array) data_get($ruleDispatch, 'params', []);

            $output['attributes'] = ['wire:click' => "\$dispatch('{$event}', " . Js::from($params) . ")"];
        }
    }
}
