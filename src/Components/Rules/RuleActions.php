<?php

namespace PowerComponents\LivewirePowerGrid\Components\Rules;

use Closure;
use Livewire\Wireable;

class RuleActions implements Wireable
{
    public array $rule = [];

    public string $forAction = '';

    public function __construct(string $button)
    {
        $this->forAction = $button;
    }

    /**
     * Disables the button.
     */
    public function when(Closure $closure = null): RuleActions
    {
        $this->rule['when'] = $closure;

        return $this;
    }

    /**
     * Sets the button's event to be emitted.
     */
    public function dispatch(string $event = '', array $params = []): RuleActions
    {
        $this->rule['dispatch'] = [
            'event'  => $event,
            'params' => $params,
        ];

        return $this;
    }

    /**
     * Sets the button's dispatchTo to be emitted.
     */
    public function dispatchTo(string $to = '', string $event = '', array $params = []): RuleActions
    {
        $this->rule['redirect']   = [];
        $this->rule['dispatchTo'] = [
            'to'     => $to,
            'event'  => $event,
            'params' => $params,
        ];

        return $this;
    }

    /**
     * Sets the button's given attribute to the given value.
     */
    public function setAttribute(string $attribute = null, string|array $value = null): RuleActions
    {
        $this->rule['setAttribute'][] = [
            'attribute' => $attribute,
            'value'     => $value,
        ];

        return $this;
    }

    /**
     * Hides the button.
     */
    public function hide(): RuleActions
    {
        $this->rule['hide'] = true;

        return $this;
    }

    /**
     * Disables the button.
     */
    public function disable(): RuleActions
    {
        $this->rule['disable'] = true;

        return $this;
    }

    /**
     * Sets button's redirect URL.
     */
    public function redirect(Closure $closure = null, string $target = '_blank'): RuleActions
    {
        $this->rule['emit']     = [];
        $this->rule['redirect'] = [
            'closure' => $closure,
            'target'  => $target,
        ];

        return $this;
    }

    /**
     * Sets the button caption value.
     */
    public function slot(string $slot): RuleActions
    {
        $this->rule['slot'] = $slot;

        return $this;
    }

    public function bladeComponent(string $component, array $params): RuleActions
    {
        $this->rule['bladeComponent'] = [
            'component' => $component,
            'params'    => $params,
        ];

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
