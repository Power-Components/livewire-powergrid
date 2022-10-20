<?php

namespace PowerComponents\LivewirePowerGrid;

final class Button
{
    public string $caption = '';

    public string $route = '';

    public string $class = '';

    public string $method = 'get';

    public string $view = '';

    public string $event = '';

    public bool $can = true;

    public string $target = '_blank';

    public string $to = '';

    public string $tooltip = '';

    public bool $toggleDetail = false;

    public bool $singleParam = false;

    public string $bladeComponent = '';

    public string $browserEvent = '';

    public array $params = [];

    public ?string $id = null;

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
    public static function make(string $action, string $caption): self
    {
        return (new static($action))
            ->caption($caption);
    }

    /**
     * Button text in view
     */
    public function caption(string $caption): Button
    {
        $this->caption = $caption;

        return $this;
    }

    /**
     * Route string
     */
    public function route(string $route, array $params, bool $singleParam = false): Button
    {
        $this->route       = $route;
        $this->params      = $params;
        $this->singleParam = $singleParam;

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
     * openModal using wire-elements
     * @see https://github.com/wire-elements/modal
     */
    public function openModal(string $component, array $params, bool $singleParam = false): Button
    {
        $this->view        = $component;
        $this->params      = $params;
        $this->singleParam = $singleParam;
        $this->method      = 'get';
        $this->route       = '';
        $this->event       = '';

        return $this;
    }

    /**
     * Livewire emit
     */
    public function emit(string $event, array $params, bool $singleParam = false): Button
    {
        $this->event       = $event;
        $this->params      = $params;
        $this->singleParam = $singleParam;
        $this->route       = '';

        return $this;
    }

    /**
     * Add Livewire emitTo
     *
     */
    public function emitTo(string $to, string $event, array $param, bool $singleParam = false): Button
    {
        $this->to          = $to;
        $this->event       = $event;
        $this->params      = $param;
        $this->singleParam = $singleParam;
        $this->route       = '';

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
     * toggleDetail
     */
    public function toggleDetail(): Button
    {
        $this->toggleDetail = true;

        return $this;
    }

    /**
     * Add Blade Component
     */
    public function bladeComponent(string $component, array $params): Button
    {
        $this->bladeComponent  = $component;
        $this->params          = $params;

        return $this;
    }

    /**
     * Alpine Dispatch Browser Events
     */
    public function dispatch(string $event, array $params): Button
    {
        $this->browserEvent  = $event;
        $this->params        = $params;
        $this->route         = '';

        return $this;
    }

    /**
     * Add custom id
     */
    public function id(string $value = null): Button
    {
        $this->id     = $value;

        return $this;
    }
}
