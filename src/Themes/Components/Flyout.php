<?php

namespace PowerComponents\LivewirePowerGrid\Themes\Components;

/**
 * CSS class tokens for the filter flyout, the drawer that slides in from the
 * left or right edge when `livewire-powergrid.filter` is set to `flyout`.
 */
class Flyout
{
    use HasProperties;

    public function view(string $view): self
    {
        $this->properties['view'] = $view;

        return $this;
    }

    /** Backdrop rendered behind the panel. */
    public function overlay(string $overlay): self
    {
        $this->properties['overlay'] = $overlay;

        return $this;
    }

    /** Classes shared by both drawer sides. */
    public function panel(string $panel): self
    {
        $this->properties['panel'] = $panel;

        return $this;
    }

    /** Classes applied on top of `panel()` when the drawer is anchored left. */
    public function panelLeft(string $panelLeft): self
    {
        $this->properties['panelLeft'] = $panelLeft;

        return $this;
    }

    /** Classes applied on top of `panel()` when the drawer is anchored right. */
    public function panelRight(string $panelRight): self
    {
        $this->properties['panelRight'] = $panelRight;

        return $this;
    }

    public function header(string $header): self
    {
        $this->properties['header'] = $header;

        return $this;
    }

    public function title(string $title): self
    {
        $this->properties['title'] = $title;

        return $this;
    }

    public function close(string $close): self
    {
        $this->properties['close'] = $close;

        return $this;
    }

    public function body(string $body): self
    {
        $this->properties['body'] = $body;

        return $this;
    }

    public function footer(string $footer): self
    {
        $this->properties['footer'] = $footer;

        return $this;
    }

    public function clearAll(string $clearAll): self
    {
        $this->properties['clearAll'] = $clearAll;

        return $this;
    }
}
