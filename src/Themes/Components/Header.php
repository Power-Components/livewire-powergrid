<?php

namespace PowerComponents\LivewirePowerGrid\Themes\Components;

use Closure;

class Header
{
    use HasProperties;

    public function view(string $view): self
    {
        $this->properties['view'] = $view;

        return $this;
    }

    public function layout(Closure $callback): self
    {
        $component = new Layout();

        $callback($component);
        $this->properties['layout'] = $component->toArray();

        return $this;
    }

    public function searchBox(Closure $callback): self
    {
        $component = new Component();

        if ($this->baseView) {
            $component->setBaseView($this->baseView);
        }

        $callback($component);
        $this->properties['searchBox'] = $component->toArray();

        return $this;
    }
}
