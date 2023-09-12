<?php

namespace PowerComponents\LivewirePowerGrid\Components\Rules\Support;

use Illuminate\Support\Js;

class DispatchToRule
{
    public function apply(array $ruleData, array &$output): void
    {
        if ($ruleDispatch = (array) data_get($ruleData, 'dispatchTo', [])) {
            $to     = strval(data_get($ruleDispatch, 'to'));
            $event  = strval(data_get($ruleDispatch, 'event'));
            $params = (array) data_get($ruleDispatch, 'params');

            $output['attributes'] = ['wire:click' => "\$dispatchTo('{$to}',{$event}', " . Js::from($params) . ")"];
        }
    }
}
