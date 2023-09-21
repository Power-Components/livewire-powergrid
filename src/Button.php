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
 * @method static tooltip(string $title)
 * @method static route(string $route, array $params)
 * @method static method(string $method)
 * @method static target(string $target) _blank, _self, _top, _parent, null
 * @method static render(\Closure $closure)
 * @method static bladeComponent(string $component, array $params)
 * @method static can(bool|\Closure $allowed = true)
 * @method static id(string $id = null)
 *
 */
final class Button implements Wireable
{
    use Macroable;

    public ?string $slot = '';

    public string $class = '';

    public array|\Closure $params = [];

    public array $dynamicProperties = [];

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
     * Class string in view: class="foo"
     */
    public function class(string $classAttr): Button
    {
        $this->class = $classAttr;

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
