<?php

namespace PowerComponents\LivewirePowerGrid\Themes\Components;

class Component
{
    use HasProperties;

    public function view(string $view): self
    {
        $this->properties['view'] = $view;

        return $this;
    }

    public function base(string $base): self
    {
        $this->properties['base'] = $base;

        return $this;
    }

    public function input(string $input): self
    {
        $this->properties['input'] = $input;

        return $this;
    }

    public function select(string $select): self
    {
        $this->properties['select'] = $select;

        return $this;
    }

    public function th(string $th): self
    {
        $this->properties['th'] = $th;

        return $this;
    }

    public function label(string $label): self
    {
        $this->properties['label'] = $label;

        return $this;
    }

    public function clickable(string $clickable): self
    {
        $this->properties['clickable'] = $clickable;

        return $this;
    }

    public function error(string $error): self
    {
        $this->properties['error'] = $error;

        return $this;
    }

    public function container(string $container): self
    {
        $this->properties['container'] = $container;

        return $this;
    }

    public function relativeMain(string $relativeMain): self
    {
        $this->properties['relativeMain'] = $relativeMain;

        return $this;
    }

    public function iconSearchWrapper(string $iconSearchWrapper): self
    {
        $this->properties['iconSearchWrapper'] = $iconSearchWrapper;

        return $this;
    }

    public function iconCloseWrapper(string $iconCloseWrapper): self
    {
        $this->properties['iconCloseWrapper'] = $iconCloseWrapper;

        return $this;
    }

    public function iconClose(string $iconClose): self
    {
        $this->properties['iconClose'] = $iconClose;

        return $this;
    }

    public function iconSearch(string $iconSearch): self
    {
        $this->properties['iconSearch'] = $iconSearch;

        return $this;
    }
}
