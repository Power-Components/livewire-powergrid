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

    /**
     *
     * @var array<int|string, string> $param
     */
    public array $param = [];

    public array $browserEventParam = [];

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
    public function route(string $route, array $param, bool $singleParam = false): Button
    {
        $this->route       = $route;
        $this->param       = $param;
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
     * @return $this
     */
    public function method(string $method): Button
    {
        $this->method = $method;

        return $this;
    }

    /**
     * openModal
     * @param string $component modal component
     * @param array<int, string> $param modal parameters
     * @param boolean $singleParam parameter is single parameter
     * @return $this
     */
    public function openModal(string $component, array $param, bool $singleParam = false): Button
    {
        $this->view        = $component;
        $this->param       = $param;
        $this->singleParam = $singleParam;
        $this->method      = 'get';
        $this->route       = '';
        $this->event       = '';

        return $this;
    }

    /**
     * emit
     * @param string $event event name
     * @param array<int, string> $param parameters
     * @param boolean $singleParam parameter is single parameter
     * @return $this
     */
    public function emit(string $event, array $param, bool $singleParam = false): Button
    {
        $this->event       = $event;
        $this->param       = $param;
        $this->singleParam = $singleParam;
        $this->route       = '';

        return $this;
    }

    /**
     * emitTo
     * @param string $to component
     * @param string $event event name
     * @param array<int, string> $param parameters
     * @param boolean $singleParam parameter is single parameter
     * @return $this
     */
    public function emitTo(string $to, string $event, array $param, bool $singleParam = false): Button
    {
        $this->to          = $to;
        $this->event       = $event;
        $this->param       = $param;
        $this->singleParam = $singleParam;
        $this->route       = '';

        return $this;
    }

    /**
     * can
     * @param bool $can can
     * @return $this
     */
    public function can(bool $can = true): Button
    {
        $this->can = $can;

        return $this;
    }

    /**
     * target _blank, _self, _top, _parent, null
     * @param string $target
     * @return $this
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
     * Add toggleDetail
     */
    public function toggleDetail(): Button
    {
        $this->toggleDetail = true;

        return $this;
    }

    /**
     * add Blade Component
     */
    public function bladeComponent(string $component, array $params): Button
    {
        $this->bladeComponent = $component;
        $this->param          = $params;

        return $this;
    }

    /**
     * Dispatch Browser Events
     */
    public function dispatch(string $event, array $param): Button
    {
        $this->browserEvent         = $event;
        $this->browserEventParam    = $param;
        $this->route                = '';

        return $this;
    }
}
