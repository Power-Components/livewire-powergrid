<?php

namespace PowerComponents\LivewirePowerGrid\Themes\Components;

use Closure;

class ThemeBuilder
{
    use HasProperties;

    public static function make(string $name): self
    {
        $builder = new self();
        $builder->properties['name'] = $name;

        return $builder;
    }

    public function baseView(string $baseView): self
    {
        $this->setBaseView($baseView);
        $this->properties['baseView'] = $baseView;

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
    public function header(Closure|array $callback): self
    {
        $component = new Header();

        if ($this->baseView) {
            $component->setBaseView($this->baseView);
        }

        if (is_array($callback)) {
            $component->fill($callback);
        } else {
            $callback($component);
        }

        $this->properties['header'] = $component->toArray();

        return $this;
    }

    /** @param  Closure|array<string, mixed>  $callback */
    public function table(Closure|array $callback): self
    {
        $component = new Table();

        if ($this->baseView) {
            $component->setBaseView($this->baseView);
        }

        if (is_array($callback)) {
            $component->fill($callback);
        } else {
            $callback($component);
        }

        $this->properties['table'] = $component->toArray();

        return $this;
    }

    /** @param  Closure|array<string, mixed>  $callback */
    public function footer(Closure|array $callback): self
    {
        $component = new Footer();

        if ($this->baseView) {
            $component->setBaseView($this->baseView);
        }

        if (is_array($callback)) {
            $component->fill($callback);
        } else {
            $callback($component);
        }

        $this->properties['footer'] = $component->toArray();

        return $this;
    }

    /** @param  Closure|array<string, mixed>  $callback */
    public function cols(Closure|array $callback): self
    {
        $component = new Cols();

        if (is_array($callback)) {
            $component->fill($callback);
        } else {
            $callback($component);
        }

        $this->properties['cols'] = $component->toArray();

        return $this;
    }

    /** @param  Closure|array<string, mixed>  $callback */
    public function tabs(Closure|array $callback): self
    {
        $component = new Tabs();

        if ($this->baseView) {
            $component->setBaseView($this->baseView);
        }

        if (is_array($callback)) {
            $component->fill($callback);
        } else {
            $callback($component);
        }

        $this->properties['tabs'] = $component->toArray();

        return $this;
    }
}
