<?php

namespace PowerComponents\LivewirePowerGrid\Components\Rules;

class RuleEditOnClick extends BaseRule
{
    public string $forAction = RuleManager::TYPE_EDIT_ON_CLICK;

    public function __construct(public string $column)
    {
        $this->forAction = $column;
    }

    /**
     * Disable the Edit On Click.
     */
    public function disable(): self
    {
        $this->setModifier('fieldHideEditOnClick', true);

        return $this;
    }

    /**
     * Enable the Edit On Click.
     */
    public function enable(): self
    {
        $this->setModifier('fieldHideEditOnClick', false);

        return $this;
    }
}
