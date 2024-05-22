<?php

namespace PowerComponents\LivewirePowerGrid\Components\Rules;

class RuleToggleable extends BaseRule
{
    public string $forAction = RuleManager::TYPE_TOGGLEABLE;

    public function __construct(public string $column)
    {
        $this->forAction = $column;
    }

    /**
    * Hides the Toggleable.
    */
    public function hide(): self
    {
        $this->setModifier('field_hide_toggleable', true);

        return $this;
    }

    /**
    * Shows the Toggleable.
    */
    public function show(): self
    {
        $this->setModifier('field_hide_toggleable', false);

        return $this;
    }
}
