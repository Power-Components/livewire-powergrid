<?php

namespace PowerComponents\LivewirePowerGrid\Themes\Components;

class HeaderButton
{
    use HasProperties;

    public function view(string $view): self
    {
        $this->properties['view'] = $view;

        return $this;
    }

    public function wrapper(string $wrapper): self
    {
        $this->properties['wrapper'] = $wrapper;

        return $this;
    }

    public function button(string $button): self
    {
        $this->properties['button'] = $button;

        return $this;
    }

    /**
     * Default icon component for this element (overridable per component via SetUp).
     */
    public function icon(string $icon): self
    {
        $this->properties['icon'] = $icon;

        return $this;
    }

    public function iconClass(string $iconClass): self
    {
        $this->properties['iconClass'] = $iconClass;

        return $this;
    }

    public function label(string $label): self
    {
        $this->properties['label'] = $label;

        return $this;
    }

    public function menu(string $menu): self
    {
        $this->properties['menu'] = $menu;

        return $this;
    }

    public function menuItem(string $menuItem): self
    {
        $this->properties['menuItem'] = $menuItem;

        return $this;
    }

    public function badge(string $badge): self
    {
        $this->properties['badge'] = $badge;

        return $this;
    }

    public function pill(string $pill): self
    {
        $this->properties['pill'] = $pill;

        return $this;
    }

    public function pillClearAll(string $pillClearAll): self
    {
        $this->properties['pillClearAll'] = $pillClearAll;

        return $this;
    }
}
