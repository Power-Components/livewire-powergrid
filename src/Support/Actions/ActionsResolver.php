<?php

namespace PowerComponents\LivewirePowerGrid\Support\Actions;

use PowerComponents\Turbine\Support\Actions\ActionsResolver as TurbineActionsResolver;

class ActionsResolver extends TurbineActionsResolver
{
    /**
     * @param  array<string, mixed>  $attributes
     * @return array<string, mixed>
     */
    protected function publicAttributes(array $attributes): array
    {
        return $attributes;
    }
}
