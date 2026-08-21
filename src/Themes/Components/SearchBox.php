<?php

namespace PowerComponents\LivewirePowerGrid\Themes\Components;

class SearchBox
{
    use HasProperties;

    public function view(string $view): self
    {
        $this->properties['view'] = $view;

        return $this;
    }

    public function container(string $container): self
    {
        $this->properties['container'] = $container;

        return $this;
    }

    public function relativeMain(string $relativeMain): self
    {
        $this->properties['relativeMain'] = $relativeMain;

        return $this;
    }

    public function input(string $input): self
    {
        $this->properties['input'] = $input;

        return $this;
    }

    public function iconSearchWrapper(string $iconSearchWrapper): self
    {
        $this->properties['iconSearchWrapper'] = $iconSearchWrapper;

        return $this;
    }

    public function iconCloseWrapper(string $iconCloseWrapper): self
    {
        $this->properties['iconCloseWrapper'] = $iconCloseWrapper;

        return $this;
    }

    public function iconClose(string $iconClose): self
    {
        $this->properties['iconClose'] = $iconClose;

        return $this;
    }

    public function iconSearch(string $iconSearch): self
    {
        $this->properties['iconSearch'] = $iconSearch;

        return $this;
    }

    /**
     * Icon component (or framework icon name) used in the search input.
     */
    public function icon(string $icon): self
    {
        $this->properties['icon'] = $icon;

        return $this;
    }

    public function iconClear(string $iconClear): self
    {
        $this->properties['iconClear'] = $iconClear;

        return $this;
    }
}
