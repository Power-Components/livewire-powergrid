<?php

namespace PowerComponents\LivewirePowerGrid\Components\Rules\Support;

use Illuminate\Support\Js;
use Illuminate\View\ComponentAttributeBag;

class SetAttributeRule
{
    public function apply(array $ruleData, array &$output): void
    {
        $ruleAttributes = (array)(data_get($ruleData, 'setAttribute', []));

        $attributeBag   = new ComponentAttributeBag();
        $attributeValue = null;

        /** @var array $ruleAttribute */
        foreach ($ruleAttributes as $ruleAttribute) {
            if (is_string($ruleAttribute['value'])) {
                $attributeValue = $ruleAttribute['value'];
            }

            if (is_array($ruleAttribute['value'])) {
                if (is_array($ruleAttribute['value'][1])) {
                    $attributeValue = $ruleAttribute['value'][0] . '(' . Js::from($ruleAttribute['value'][1]) . ')';
                } else {
                    $attributeValue = $ruleAttribute['value'][0] . '(' . $ruleAttribute['value'][1] . ')';
                }
            }

            $attributeBag = $attributeBag->merge([$ruleAttribute['attribute'] => $attributeValue]);
        }

        $output['attributes'] = $attributeBag->getAttributes();
    }
}
