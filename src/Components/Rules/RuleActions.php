<?php

namespace PowerComponents\LivewirePowerGrid\Components\Rules;

use Illuminate\Support\Traits\Macroable;

class RuleActions extends BaseRule
{
    use Macroable;

    public string $forAction = RuleManager::TYPE_ACTIONS;

    public function __construct(string $button)
    {
        $this->forAction = $button;
    }

    /**
     * Sets the button's given attribute to the given value.
     */
    public function setAttribute(?string $attribute = null, string|array|null $value = null): self
    {
        $this->pushModifier('setAttribute', [
            'attribute' => $attribute,
            'value'     => $value,
        ]);

        return $this;
    }

    /**
     * Hides the button.
     */
    public function hide(): self
    {
        $this->setModifier('hide', true);

        return $this;
    }

    /**
     * Sets the button caption value.
     */
    public function slot(string $slot): self
    {
        $this->setModifier('slot', $slot);

        return $this;
    }

    public function bladeComponent(string $component, array $params): self
    {
        $this->setModifier('bladeComponent', [
            'component' => $component,
            'params'    => $params,
        ]);

        return $this;
    }
}
