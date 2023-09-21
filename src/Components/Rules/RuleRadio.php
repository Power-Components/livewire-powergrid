<?php

namespace PowerComponents\LivewirePowerGrid\Components\Rules;

use Closure;
use Livewire\Wireable;

class RuleRadio implements Wireable
{
    public array $rule = [];

    public string $forAction = RuleManager::TYPE_RADIO;

    /**
     * Disables the radio.
     */
    public function when(Closure $closure = null): RuleRadio
    {
        $this->rule['when'] = $closure;

        return $this;
    }

    /**Sets the radio's given attribute to the given value
     * .
     */
    public function setAttribute(string $attribute = null, string $value = null): RuleRadio
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
    public function hide(): RuleRadio
    {
        $this->rule['hide'] = true;

        return $this;
    }

    /**
     * Disables the button.
     */
    public function disable(): RuleRadio
    {
        $this->rule['disable'] = true;

        return $this;
    }

    public function applyRowClasses(string $attrClass = ''): RuleRadio
    {
        $this->rule['rowClasses'] = $attrClass;

        return $this;
    }

    public function toLivewire(): array
    {
        return (array) $this;
    }

    public static function fromLivewire($value)
    {
        return $value;
    }
}
