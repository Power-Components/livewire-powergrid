<?php

namespace PowerComponents\LivewirePowerGrid\Rules;

use Closure;

class RuleCheckbox
{
    public array $rule = [];

    public string $forAction = RuleManager::TYPE_CHECKBOX;

    /**
     * Disables the button.
     */
    public function when(Closure $closure = null): RuleCheckbox
    {
        $this->rule['when'] = $closure;

        return $this;
    }

    /**
     * Sets the button's given attribute to the given value.
     */
    public function setAttribute(string $attribute = null, string $value = null): RuleCheckbox
    {
        $this->rule['setAttribute'] = [
            'attribute' => $attribute,
            'value'     => $value,
        ];

        return $this;
    }

    /**
     * Hides the button.
     */
    public function hide(): RuleCheckbox
    {
        $this->rule['hide'] = true;

        return $this;
    }

    /**
     * Disables the button.
     */
    public function disable(): RuleCheckbox
    {
        $this->rule['disable'] = true;

        return $this;
    }
}
