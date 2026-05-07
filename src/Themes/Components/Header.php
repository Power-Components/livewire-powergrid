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

    public function layout(Closure|array $callback): self
    {
        $component = new Layout();

        if (is_array($callback)) {
            $component->fill($callback);
        } else {
            $callback($component);
        }

        $this->properties['layout'] = $component->toArray();

        return $this;
    }

    public function searchBox(Closure|array $callback): self
    {
        $component = new SearchBox();

        if ($this->baseView) {
            $component->setBaseView($this->baseView);
        }

        if (is_array($callback)) {
            $component->fill($callback);
        } else {
            $callback($component);
        }

        $this->properties['searchBox'] = $component->toArray();

        return $this;
    }
}
