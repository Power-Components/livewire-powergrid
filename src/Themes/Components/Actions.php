<?php

namespace PowerComponents\LivewirePowerGrid\Themes\Components;

class Actions
{
    public string $btnClass = '';

    public string $btnStyle = '';

    public string $tdBodyClass = '';

    public string $tdBodyStyle = '';

    public string $headerBtnClass = '';

    public string $headerBtnStyle = '';

    /**
     * @param string $attrClass
     * @param string $attrStyle
     * @return $this
     */
    public function rowsBtn(string $attrClass = '', string $attrStyle = ''): Actions
    {
        $this->btnClass = $attrClass;
        $this->btnStyle = $attrStyle;

        return $this;
    }

    /**
     * @param string $attrClass
     * @param string $attrStyle
     * @return $this
     */
    public function tdBody(string $attrClass = '', string $attrStyle = ''): Actions
    {
        $this->tdBodyClass = $attrClass;
        $this->tdBodyStyle = $attrStyle;

        return $this;
    }

    /**
     * @param string $attrClass
     * @param string $attrStyle
     * @return $this
     */
    public function headerBtn(string $attrClass = '', string $attrStyle = ''): Actions
    {
        $this->headerBtnClass = $attrClass;
        $this->headerBtnStyle = $attrStyle;

        return $this;
    }
}
