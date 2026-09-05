<?php

namespace PowerComponents\LivewirePowerGrid\Themes\Components;

use Closure;

class Table
{
    use HasProperties;

    public function view(string $view): self
    {
        $this->properties['view'] = $view;

        return $this;
    }

    /** @param  Closure|array<string, mixed>  $callback */
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

    /** @param  Closure|array<string, mixed>  $callback */
    public function body(Closure|array $callback): self
    {
        $component = new Body();

        if (is_array($callback)) {
            $component->fill($callback);
        } else {
            $callback($component);
        }

        $this->properties['body'] = $component->toArray();

        return $this;
    }

    /** @param  Closure|array<string, mixed>  $callback */
    public function checkbox(Closure|array $callback): self
    {
        $component = new Checkbox();

        if (is_array($callback)) {
            $component->fill($callback);
        } else {
            $callback($component);
        }

        $this->properties['checkbox'] = $component->toArray();

        return $this;
    }

    /** @param  Closure|array<string, mixed>  $callback */
    public function radio(Closure|array $callback): self
    {
        $component = new Radio();

        if (is_array($callback)) {
            $component->fill($callback);
        } else {
            $callback($component);
        }

        $this->properties['radio'] = $component->toArray();

        return $this;
    }
}
