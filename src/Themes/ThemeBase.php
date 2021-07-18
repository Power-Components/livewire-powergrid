<?php

namespace PowerComponents\LivewirePowerGrid\Themes;

class ThemeBase extends AbstractTheme
{
    public function root(): string
    {
        return $this->base . "" . $this->name . "\\";
    }

    public function layout(): Components\Layout
    {
        return Theme::layout()
            ->table($this->root() . "table-base")
            ->header($this->root() . "header")
            ->pagination($this->root() . "pagination")
            ->message($this->root() . "message")
            ->footer($this->root() . "footer");
    }

    public function toggleable(): Components\Toggleable
    {
        return Theme::toggleable()
            ->view($this->root() . "toggleable");
    }

    public function apply(): ThemeBase
    {
        $this->table             = $this->table();
        $this->footer            = $this->footer();
        $this->cols              = $this->cols();
        $this->editable          = $this->editable();
        $this->layout            = $this->layout();
        $this->toggleable        = $this->toggleable();
        $this->actions           = $this->actions();
        $this->checkbox          = $this->checkbox();
        $this->filterBoolean     = $this->filterBoolean();
        $this->filterDatePicker  = $this->filterDatePicker();
        $this->filterMultiSelect = $this->filterMultiSelect();
        $this->filterNumber      = $this->filterNumber();
        $this->filterSelect      = $this->filterSelect();
        $this->filterInputText   = $this->filterInputText();

        return $this;
    }
}
