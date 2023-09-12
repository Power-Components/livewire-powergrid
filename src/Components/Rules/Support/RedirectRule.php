<?php

namespace PowerComponents\LivewirePowerGrid\Components\Rules\Support;

class RedirectRule
{
    public function apply(array $ruleData, array &$output): void
    {
        if ($redirectToUrl = data_get($ruleData, 'redirect.url')) {
            $output['attributes'] = ['href' => $redirectToUrl];
            $output['remove']     = 'wire:click';
            $output['component']  = 'a';
        }
    }
}
