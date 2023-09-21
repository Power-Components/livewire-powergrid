<?php

namespace PowerComponents\LivewirePowerGrid\Components\Rules\Support;

class RedirectRule
{
    public function apply(array $ruleData): array
    {
        $output = [];

        $redirectToUrl = data_get($ruleData, 'url');

        $output['attributes'] = ['href' => $redirectToUrl, 'target' => '_blank'];
        $output['remove']     = 'wire:click';
        $output['component']  = 'a';

        return $output;
    }
}
