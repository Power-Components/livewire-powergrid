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
        $this->properties['layout'] = $this->buildSection(new Layout(), $callback);

        return $this;
    }

    /** @param  Closure|array<string, mixed>  $callback */
    public function header(Closure|array $callback): self
    {
        $this->properties['header'] = $this->buildSection(new Header(), $callback, true);

        return $this;
    }

    /** @param  Closure|array<string, mixed>  $callback */
    public function table(Closure|array $callback): self
    {
        $this->properties['table'] = $this->buildSection(new Table(), $callback, true);

        return $this;
    }

    /** @param  Closure|array<string, mixed>  $callback */
    public function footer(Closure|array $callback): self
    {
        $this->properties['footer'] = $this->buildSection(new Footer(), $callback, true);

        return $this;
    }

    /** @param  Closure|array<string, mixed>  $callback */
    public function cols(Closure|array $callback): self
    {
        $this->properties['cols'] = $this->buildSection(new Cols(), $callback);

        return $this;
    }

    /** @param  Closure|array<string, mixed>  $callback */
    public function tabs(Closure|array $callback): self
    {
        $this->properties['tabs'] = $this->buildSection(new Tabs(), $callback, true);

        return $this;
    }
}
