<?php

namespace PowerComponents\LivewirePowerGrid\Components\Rules;

class RuleRadio extends BaseRule
{
    public string $forAction = RuleManager::TYPE_RADIO;

    /**Sets the radio's given attribute to the given value
     */
    public function setAttribute(string $attribute = null, string $value = null): self
    {
        $this->setModifier('setAttribute', [
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
     * Disables the button.
     */
    public function disable(): self
    {
        $this->setModifier('disable', true);

        return $this;
    }

    public function applyRowClasses(string $attrClass = ''): self
    {
        $this->setModifier('rowClasses', $attrClass);

        return $this;
    }
}
