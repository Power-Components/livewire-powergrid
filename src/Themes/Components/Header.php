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

    /** @param  Closure|array<string, mixed>  $callback */
    public function toggleColumns(Closure|array $callback): self
    {
        $this->properties['toggleColumns'] = $this->headerButton($callback);

        return $this;
    }

    /** @param  Closure|array<string, mixed>  $callback */
    public function softDeletes(Closure|array $callback): self
    {
        $this->properties['softDeletes'] = $this->headerButton($callback);

        return $this;
    }

    /** @param  Closure|array<string, mixed>  $callback */
    public function filters(Closure|array $callback): self
    {
        $this->properties['filters'] = $this->headerButton($callback);

        return $this;
    }

    /** @param  Closure|array<string, mixed>  $callback */
    public function filterBuilder(Closure|array $callback): self
    {
        $this->properties['filterBuilder'] = $this->headerButton($callback);

        return $this;
    }

    /** @param  Closure|array<string, mixed>  $callback */
    public function export(Closure|array $callback): self
    {
        $this->properties['export'] = $this->headerButton($callback);

        return $this;
    }

    /** @param  Closure|array<string, mixed>  $callback */
    public function enabledFilters(Closure|array $callback): self
    {
        $this->properties['enabledFilters'] = $this->headerButton($callback);

        return $this;
    }

    /**
     * @param  Closure|array<string, mixed>  $callback
     * @return array<string, mixed>
     */
    private function headerButton(Closure|array $callback): array
    {
        $component = new HeaderButton();

        if ($this->baseView) {
            $component->setBaseView($this->baseView);
        }

        if (is_array($callback)) {
            $component->fill($callback);
        } else {
            $callback($component);
        }

        return $component->toArray();
    }
}
