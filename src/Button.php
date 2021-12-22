<?php

namespace PowerComponents\LivewirePowerGrid;

final class Button
{
    public string $action = '';

    public string $caption = '';

    public string $route = '';

    public string $class = '';

    public string $method = 'get';

    public string $view = '';

    public string $event = '';

    public bool $can = true;

    public string $target = '_blank';

    /**
     *
     * @var array<int, string> $param
     */
    public array $param = [];

    /**
     * Button constructor.
     * @param string $action
     */
    public function __construct(string $action)
    {
        $this->action = $action;
    }

    /**
     * @param string $action
     * @return Button
     */
    public static function add(string $action = ''): Button
    {
        return new Button($action);
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
     * @param string $route
     * @param array<int, string> $param
     * @return $this
     */
    public function route(string $route, array $param): Button
    {
        $this->route = $route;
        $this->param = $param;

        return $this;
    }

    /**
     * Class string in view: class="bla bla bla"
     * @return $this
     */
    public function class(string $class_attr): Button
    {
        $this->class = $class_attr;

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
     * @return $this
     */
    public function openModal(string $component, array $param): Button
    {
        $this->view   = $component;
        $this->param  = $param;
        $this->method = 'get';
        $this->route  = '';
        $this->event  = '';

        return $this;
    }

    /**
     * emit
     * @param string $event event name
     * @param array<int, string> $param parameters
     * @return $this
     */
    public function emit(string $event, array $param): Button
    {
        $this->event   = $event;
        $this->param   = $param;
        $this->route   = '';

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
}
