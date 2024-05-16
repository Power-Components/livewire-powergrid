<?php

namespace PowerComponents\LivewirePowerGrid\Components\Rules;

use Closure;

class RuleActions extends BaseRule
{
    public string $forAction = RuleManager::TYPE_ACTIONS;

    public function __construct(string $button)
    {
        $this->forAction = $button;
    }

    /**
     * Sets the button's event to be emitted.
     */
    public function dispatch(string $event = '', array $params = []): self
    {
        $this->setModifier('dispatch', [
            'event'  => $event,
            'params' => $params,
        ]);

        return $this;
    }

    /**
     * Sets the button's dispatchTo to be emitted.
     */
    public function dispatchTo(string $to = '', string $event = '', array $params = []): self
    {
        $this->setModifier('redirect', []);
        $this->setModifier('dispatchTo', [
            'to'     => $to,
            'event'  => $event,
            'params' => $params,
        ]);

        return $this;
    }

    /**
     * Sets the button's given attribute to the given value.
     */
    public function setAttribute(string $attribute = null, string|array $value = null): self
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
     * Shows the button.
     */
    public function show(): self
    {
        $this->setModifier('show', false);

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

    /**
     * Enables the button.
     */
    public function enable(): self
    {
        $this->setModifier('disable', false);

        return $this;
    }

    /**
     * Sets button's redirect URL.
     */
    public function redirect(Closure $closure = null, string $target = '_blank'): self
    {
        $this->setModifier('emit', []);
        $this->setModifier('redirect', [
            'closure' => $closure,
            'target'  => $target,
        ]);

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
