<?php

namespace PowerComponents\LivewirePowerGrid\Components\Rules\Support;

use Illuminate\Support\Facades\Blade;
use Illuminate\View\ComponentAttributeBag;

class BladeComponentRule
{
    public function apply(array $ruleData): array
    {
        $output = [];

        if ($component = strval(data_get($ruleData, 'component'))) {
            $params = data_get($ruleData, 'params');

            $html = Blade::render('<x-dynamic-component
                :component="$component"
                :attributes="$params"
                />', [
                'component' => $component,
                'params'    => new ComponentAttributeBag((array) $params),
            ]);

            $output['blade'] = $html;
        }

        return $output;
    }
}
