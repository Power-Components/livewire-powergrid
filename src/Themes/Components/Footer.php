<?php

namespace PowerComponents\LivewirePowerGrid\Themes\Components;

use Closure;

class Footer
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

    public function pagination(Closure|array|string $callback): self
    {
        $component = is_string($callback)
            ? new Pagination($callback)
            : new Pagination();

        if ($this->baseView) {
            $component->setBaseView($this->baseView);
        }

        if (is_array($callback)) {
            $component->fill($callback);
        } elseif ($callback instanceof Closure) {
            $callback($component);
        }

        $this->properties['pagination'] = $component->toArray();

        return $this;
    }
}
