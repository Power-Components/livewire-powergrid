<?php

namespace PowerComponents\LivewirePowerGrid\Support\Actions;

use PowerComponents\Turbine\Response\ActionDescriptor;
use PowerComponents\Turbine\Support\Actions\{ActionsResolver as TurbineActionsResolver, DescriptorData};

class ActionsResolver extends TurbineActionsResolver
{
    protected function createDescriptor(DescriptorData $desc): ActionDescriptor
    {
        return parent::createDescriptor($desc);
    }

    protected function publicAttributes(array $attributes): array
    {
        return $attributes;
    }
}
