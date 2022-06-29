<?php

namespace PowerComponents\LivewirePowerGrid\Rules;

use Closure;

class RuleActions
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
    public function emit(string $event = '', array $params = []): RuleActions
    {
        $this->rule['redirect'] = [];
        $this->rule['emit']     = [
            'event'  => $event,
            'params' => $params,
        ];

        return $this;
    }

    /**
     * Sets the button's eventTo to be emitted.
     */
    public function emitTo(string $to = '', string $event = '', array $params = []): RuleActions
    {
        $this->rule['redirect'] = [];
        $this->rule['emitTo']   = [
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
        $this->rule['emit']      = [];
        $this->rule['redirect']  = [
            'closure' => $closure,
            'target'  => $target,
        ];

        return $this;
    }

    /**
     * Sets the button caption value.
     */
    public function caption(string $caption): RuleActions
    {
        $this->rule['caption'] = $caption;

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
}
