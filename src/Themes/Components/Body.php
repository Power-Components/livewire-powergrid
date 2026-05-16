<?php

namespace PowerComponents\LivewirePowerGrid\Themes\Components;

use Closure;

class Body
{
    use HasProperties;

    public function tr(Closure|string $callback): self
    {
        if (is_string($callback)) {
            $this->properties['tr'] = $callback;

            return $this;
        }

        $component = new Tr();

        $callback($component);

        $this->properties['tr'] = $component->toArray();

        return $this;
    }

    public function td(Closure|string $callback): self
    {
        if (is_string($callback)) {
            $this->properties['td'] = $callback;

            return $this;
        }

        $component = new Td();

        $callback($component);

        $this->properties['td'] = $component->toArray();

        return $this;
    }
}
