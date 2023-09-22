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

        foreach ($ruleAttribute as $rule) {
            if (is_array($rule['value'])) {
                if (is_array($rule['value'][1])) {
                    $attributeValue = $rule['value'][0] . '(' . Js::from($rule['value'][1]) . ')';
                } else {
                    $attributeValue = $rule['value'][0] . '(' . $rule['value'][1] . ')';
                }
            } else {
                $attributeValue = $rule['value'];
            }

            $attributeBag = $attributeBag->merge([$rule['attribute'] => $attributeValue]);
        }

        $output['attributes'] = $attributeBag->getAttributes();

        return $output;
    }
}
