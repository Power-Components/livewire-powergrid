<?php

namespace PowerComponents\LivewirePowerGrid\Themes\Components;

use Closure;

class Filter
{
    use HasProperties;

    public function label(string $label): self
    {
        $this->properties['label'] = $label;

        return $this;
    }

    public function input(string $input): self
    {
        $this->properties['input'] = $input;

        return $this;
    }

    public function boolean(Closure $callback): self
    {
        $component = new Component();
        $callback($component);
        $this->properties['boolean'] = $component->toArray();

        return $this;
    }

    public function datePicker(Closure $callback): self
    {
        $component = new Component();
        $callback($component);
        $this->properties['datePicker'] = $component->toArray();

        return $this;
    }

    public function multiSelect(Closure $callback): self
    {
        $component = new Component();
        $callback($component);
        $this->properties['multiSelect'] = $component->toArray();

        return $this;
    }

    public function number(Closure $callback): self
    {
        $component = new Component();
        $callback($component);
        $this->properties['number'] = $component->toArray();

        return $this;
    }

    public function select(Closure $callback): self
    {
        $component = new Component();
        $callback($component);
        $this->properties['select'] = $component->toArray();

        return $this;
    }

    public function inputText(Closure $callback): self
    {
        $component = new Component();
        $callback($component);
        $this->properties['inputText'] = $component->toArray();

        return $this;
    }
}
