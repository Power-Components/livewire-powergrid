<?php

namespace PowerComponents\LivewirePowerGrid\Themes\Components;

class Checkbox
{
    use HasProperties;

    public function th(string $th): self
    {
        $this->properties['th'] = $th;

        return $this;
    }

    public function base(string $base): self
    {
        $this->properties['base'] = $base;

        return $this;
    }

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
}
