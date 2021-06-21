<?php

namespace PowerComponents\LivewirePowerGrid\Themes\Components;

class Actions
{
    public string $btnClass = '';

    public string $btnStyle = '';

    public string $tdBodyClass = '';

    public string $tdBodyStyle = '';

    public function btn(string $attrClass, string $attrStyle=''): Actions
    {
        $this->btnClass    = $attrClass;
        $this->btnStyle    = $attrStyle;

        return $this;
    }

    public function tdBody(string $attrClass, string $attrStyle=''): Actions
    {
        $this->tdBodyClass = $attrClass;
        $this->tdBodyStyle = $attrStyle;

        return $this;
    }
}
