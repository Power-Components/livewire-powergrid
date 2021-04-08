<?php


namespace PowerComponents\LivewirePowerGrid;

class Button
{

    public string $action = '';
    public string $caption = '';
    public string $route = '';
    public array $param = [];
    public string $class = '';
    public array $i = [];

    /**
     * Button constructor.
     * @param string $action
     */
    public function __construct( string $action )
    {
        $this->action = $action;
    }

    /**
     * @param string|null $action
     * @return Button
     */
    public static function add( string $action = null ): Button
    {
        return new static($action);
    }

    /**
     * Button text in view
     * @param string $caption
     * @return $this
     */
    public function caption( string $caption ): Button
    {
        $this->caption = $caption;
        return $this;
    }

    /**
     * @param string $route
     * @param array $param
     * @return $this
     */
    public function route( string $route, array $param ): Button
    {
        $this->route = $route;
        $this->param = $param;
        return $this;
    }

    /**
     * Class string in view: class="bla bla bla"
     * @param string $class_attr
     * @return $this
     */
    public function class( string $class_attr ): Button
    {
        $this->class = $class_attr;
        return $this;
    }

    /**
     * @param string $class class of i tag for fontawesome or other
     * @param string $text text of caption
     * @param bool $showCaption when false it will not display the caption
     * @return $this
     */
    public function i( string $class, string $text, bool $showCaption = false ): Button
    {
        $this->i = [
            'class' => $class,
            'text' => $text,
            'caption' => $showCaption
        ];
        return $this;
    }
}
