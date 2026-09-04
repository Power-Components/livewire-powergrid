<?php

namespace PowerComponents\LivewirePowerGrid\Themes\Components;

class Tabs
{
    use HasProperties;

    public function view(string $view): self
    {
        $this->properties['view'] = $view;

        return $this;
    }

    public function list(string $list): self
    {
        $this->properties['list'] = $list;

        return $this;
    }

    public function tab(string $tab): self
    {
        $this->properties['tab'] = $tab;

        return $this;
    }

    public function tabActive(string $tabActive): self
    {
        $this->properties['tabActive'] = $tabActive;

        return $this;
    }

    public function tabInactive(string $tabInactive): self
    {
        $this->properties['tabInactive'] = $tabInactive;

        return $this;
    }

    public function badge(string $badge): self
    {
        $this->properties['badge'] = $badge;

        return $this;
    }

    public function badgeActive(string $badgeActive): self
    {
        $this->properties['badgeActive'] = $badgeActive;

        return $this;
    }

    public function badgeInactive(string $badgeInactive): self
    {
        $this->properties['badgeInactive'] = $badgeInactive;

        return $this;
    }
}
