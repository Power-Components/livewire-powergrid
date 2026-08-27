<?php

namespace PowerComponents\LivewirePowerGrid\Themes\Components;

class Dropdown
{
    use HasProperties;

    public function view(string $view): self
    {
        $this->properties['view'] = $view;

        return $this;
    }

    public function wrapper(string $wrapper): self
    {
        $this->properties['wrapper'] = $wrapper;

        return $this;
    }

    public function trigger(string $trigger): self
    {
        $this->properties['trigger'] = $trigger;

        return $this;
    }

    public function badge(string $badge): self
    {
        $this->properties['badge'] = $badge;

        return $this;
    }

    public function panel(string $panel): self
    {
        $this->properties['panel'] = $panel;

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

    public function body(string $body): self
    {
        $this->properties['body'] = $body;

        return $this;
    }

    public function grid(string $grid): self
    {
        $this->properties['grid'] = $grid;

        return $this;
    }

    public function footer(string $footer): self
    {
        $this->properties['footer'] = $footer;

        return $this;
    }

    public function reset(string $reset): self
    {
        $this->properties['reset'] = $reset;

        return $this;
    }

    public function clear(string $clear): self
    {
        $this->properties['clear'] = $clear;

        return $this;
    }

    public function apply(string $apply): self
    {
        $this->properties['apply'] = $apply;

        return $this;
    }
}
