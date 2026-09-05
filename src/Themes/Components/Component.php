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
}
