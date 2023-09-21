<?php

namespace PowerComponents\LivewirePowerGrid\Components\Rules\Support;

use Illuminate\Support\Js;
use Illuminate\View\ComponentAttributeBag;

class SetAttributeRule
{
    public function apply(array $ruleData): array
    {
        $output        = [];
        $ruleAttribute = $ruleData;

        $attributeBag = new ComponentAttributeBag();

        if (is_array($ruleAttribute['value'])) {
            if (is_array($ruleAttribute['value'][1])) {
                $attributeValue = $ruleAttribute['value'][0] . '(' . Js::from($ruleAttribute['value'][1]) . ')';
            } else {
                $attributeValue = $ruleAttribute['value'][0] . '(' . $ruleAttribute['value'][1] . ')';
            }
        } else {
            $attributeValue = $ruleAttribute['value'];
        }

        $attributeBag = $attributeBag->merge([$ruleAttribute['attribute'] => $attributeValue]);

        $output['attributes'] = $attributeBag->getAttributes();

        return $output;
    }
}
