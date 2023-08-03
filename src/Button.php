<?php

namespace PowerComponents\LivewirePowerGrid;

use Illuminate\Support\Traits\Macroable;
use Livewire\Wireable;

/**
 * @method static dispatch(string $event, array $params)
 * @method static dispatchTo(string $component, string $event, array $params)
 * @method static dispatchSelf(string $event, array $params)
 * @method static openModal(string $component, array $params)
 * @method static parent(string $method, array $params)
 * @method static call(string $method, array $params)
 * @method static toggleDetail()
 */
final class Button implements Wireable
{
    use Macroable;

    public ?string $slot = '';

    public string $route = '';

    public string $class = '';

    public string $method = 'get';

    public bool $can = true;

    public string $target = '_blank';

    public string $tooltip = '';

    public string $bladeComponent = '';

    public array|\Closure $params = [];

    public ?string $id = null;

    public array $dynamicProperties = [];

    public ?\Closure $render = null;

    /**
     * Button constructor.
     * @param string $action
     */
    public function __construct(public string $action)
    {
    }

    public static function add(string $action = ''): Button
    {
        return new Button($action);
    }

    /**
     * Make a new Column
     */
    public static function make(string $action, ?string $slot = null): self
    {
        return (new static($action))
            ->slot($slot);
    }

    /**
     * Button text in view
     */
    public function slot(?string $slot = null): Button
    {
        $this->slot = $slot;

        return $this;
    }

    /**
     * Route string
     * @codeCoverageIgnore
     */
    public function route(string $route, array|\Closure $params): Button
    {
        $this->route  = $route;
        $this->params = $params;

        return $this;
    }

    /**
     * Class string in view: class="foo"
     */
    public function class(string $classAttr): Button
    {
        $this->class = $classAttr;

        return $this;
    }

    /**
     * Method for button
     */
    public function method(string $method): Button
    {
        $this->method = $method;

        return $this;
    }

    /**
     * Can
     *
     */
    public function can(bool $can = true): Button
    {
        $this->can = $can;

        return $this;
    }

    /**
     * target _blank, _self, _top, _parent, null
     *
     */
    public function target(string $target): Button
    {
        $this->target = $target;

        return $this;
    }

    /**
     * Add tooltip
     */
    public function tooltip(string $tooltip): Button
    {
        $this->tooltip = $tooltip;

        return $this;
    }

    /**
     * Add Blade Component
     */
    public function bladeComponent(string $component, array|\Closure $params): Button
    {
        $this->bladeComponent = $component;
        $this->params         = $params;

        return $this;
    }

    /**
     * Add custom id
     */
    public function id(string $value = null): Button
    {
        $this->id = $value;

        return $this;
    }

    /**
     * Render custom action
     */
    public function render(\Closure $closure): Button
    {
        $this->render = $closure;

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
