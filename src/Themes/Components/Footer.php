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

    /** @param  Closure|array<string, mixed>  $callback */
    public function layout(Closure|array $callback): self
    {
        $this->properties['layout'] = $this->buildSection(new Layout(), $callback);

        return $this;
    }

    /** @param  Closure|array<string, mixed>|string  $callback */
    public function pagination(Closure|array|string $callback): self
    {
        $this->properties['pagination'] = is_string($callback)
            ? $this->buildSection(new Pagination($callback), [], true)
            : $this->buildSection(new Pagination(), $callback, true);

        return $this;
    }
}
