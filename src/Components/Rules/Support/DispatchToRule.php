<?php

namespace PowerComponents\LivewirePowerGrid\Components\Rules\Support;

use Illuminate\Support\Js;
use JsonException;

class DispatchToRule
{
    /**
     * @throws JsonException
     */
    public function apply(array $ruleData): array
    {
        $output = [];

        $to     = strval(data_get($ruleData, 'to'));
        $event  = strval(data_get($ruleData, 'event'));
        $params = (array) data_get($ruleData, 'params');

        $output['attributes'] = ['wire:click' => "\$dispatchTo('{$to}','{$event}', " . Js::from($params) . ')'];

        return $output;
    }
}
