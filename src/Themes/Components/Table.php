<?php

namespace PowerComponents\LivewirePowerGrid\Themes\Components;

class Table
{
    public string $divClass = '';

    public string $divStyle = '';

    public string $tableClass = '';

    public string $tableStyle = '';

    public string $theadClass = '';

    public string $theadStyle = '';

    public string $trClass = '';

    public string $trStyle = '';

    public string $trFiltersClass = '';

    public string $trFiltersStyle = '';

    public string $thClass = '';

    public string $thStyle = '';

    public string $tbodyClass = '';

    public string $tbodyStyle = '';

    public string $trBodyClass = '';

    public string $trBodyStyle = '';

    public string $tdBodyClass = '';

    public string $tdBodyStyle = '';

    public string $tdFiltersClass = '';

    public string $tdFiltersStyle = '';

    public string $tdBodyClassTotalColumns = '';

    public string $tdBodyStyleTotalColumns = '';

    public string $trBodyClassTotalColumns = '';

    public string $trBodyStyleTotalColumns = '';

    public string $tdBodyEmptyClass = '';

    public string $tdBodyEmptyStyle = '';

    public string $thActionClass = '';

    public string $thActionStyle = '';

    public string $tdActionClass = '';

    public string $tdActionStyle = '';

    /**
     * Table constructor.
     * @param string $tableClass
     * @param string $tableStyle
     */
    public function __construct(string $tableClass, string $tableStyle = '')
    {
        $this->tableClass = $tableClass;
        $this->tableStyle = $tableStyle;
    }

    public function div(string $attrClass, string $attrStyle = ''): Table
    {
        $this->divClass = $attrClass;
        $this->divStyle = $attrStyle;

        return $this;
    }

    public function thead(string $attrClass, string $attrStyle = ''): Table
    {
        $this->theadClass = $attrClass;
        $this->theadStyle = $attrStyle;

        return $this;
    }

    public function tr(string $attrClass, string $attrStyle = ''): Table
    {
        $this->trClass = $attrClass;
        $this->trStyle = $attrStyle;

        return $this;
    }

    public function trFilters(string $attrClass, string $attrStyle = ''): Table
    {
        $this->trFiltersClass = $attrClass;
        $this->trFiltersStyle = $attrStyle;

        return $this;
    }

    public function tdFilters(string $attrClass = '', string $attrStyle = ''): Table
    {
        $this->tdFiltersClass = $attrClass;
        $this->tdFiltersStyle = $attrStyle;

        return $this;
    }

    public function th(string $attrClass, string $attrStyle = ''): Table
    {
        $this->thClass = $attrClass;
        $this->thStyle = $attrStyle;

        return $this;
    }

    public function tbody(string $attrClass, string $attrStyle = ''): Table
    {
        $this->tbodyClass = $attrClass;
        $this->tbodyStyle = $attrStyle;

        return $this;
    }

    public function trBody(string $attrClass, string $attrStyle = ''): Table
    {
        $this->trBodyClass = $attrClass;
        $this->trBodyStyle = $attrStyle;

        return $this;
    }

    public function tdBody(string $attrClass, string $attrStyle = ''): Table
    {
        $this->tdBodyClass = $attrClass;
        $this->tdBodyStyle = $attrStyle;

        return $this;
    }

    public function tdBodyTotalColumns(string $attrClass, string $attrStyle = ''): Table
    {
        $this->tdBodyClassTotalColumns = $attrClass;
        $this->tdBodyStyleTotalColumns = $attrStyle;

        return $this;
    }

    public function trBodyClassTotalColumns(string $attrClass, string $attrStyle = ''): Table
    {
        $this->trBodyClassTotalColumns = $attrClass;
        $this->trBodyStyleTotalColumns = $attrStyle;

        return $this;
    }

    public function tdBodyEmpty(string $attrClass, string $attrStyle = ''): Table
    {
        $this->tdBodyEmptyClass = $attrClass;
        $this->tdBodyEmptyStyle = $attrStyle;

        return $this;
    }

    public function thAction(string $attrClass, string $attrStyle = ''): Table
    {
        $this->thActionClass = $attrClass;
        $this->thActionStyle = $attrStyle;

        return $this;
    }

    public function tdAction(string $attrClass, string $attrStyle = ''): Table
    {
        $this->tdActionClass = $attrClass;
        $this->tdActionStyle = $attrStyle;

        return $this;
    }
}
