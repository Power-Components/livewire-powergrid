<?php

namespace PowerComponents\LivewirePowerGrid\Rules;

use Closure;

class RuleRows
{
    public array $rule = [];

    public string $forAction = RuleManager::TYPE_ROWS;

    public ?string $column = '';

    /**
     * Disables the button.
     */
    public function when(Closure $closure = null): RuleRows
    {
        $this->rule['when'] = $closure;

        return $this;
    }

    /**
     * Sets the button's given attribute to the given value.
     */
    public function setAttribute(string $attribute = null, string $value = null): RuleRows
    {
        $this->rule['setAttribute'] = [
            'attribute' => $attribute,
            'value'     => $value,
        ];

        return $this;
    }
}
