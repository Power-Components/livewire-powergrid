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
        $this->properties['layout'] = $this->buildSection(new Layout(), $callback);

        return $this;
    }

    /** @param  Closure|array<string, mixed>  $callback */
    public function body(Closure|array $callback): self
    {
        $this->properties['body'] = $this->buildSection(new Body(), $callback);

        return $this;
    }

    /** @param  Closure|array<string, mixed>  $callback */
    public function checkbox(Closure|array $callback): self
    {
        $this->properties['checkbox'] = $this->buildSection(new Checkbox(), $callback);

        return $this;
    }

    /** @param  Closure|array<string, mixed>  $callback */
    public function radio(Closure|array $callback): self
    {
        $this->properties['radio'] = $this->buildSection(new Radio(), $callback);

        return $this;
    }
}
