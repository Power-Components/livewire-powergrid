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
        $this->setModifier('fieldHideToggleable', true);

        return $this;
    }

    /**
     * Shows the Toggleable.
     */
    public function show(): self
    {
        $this->setModifier('fieldHideToggleable', false);

        return $this;
    }
}
