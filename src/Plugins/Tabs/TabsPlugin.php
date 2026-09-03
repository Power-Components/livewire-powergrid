<?php

namespace PowerComponents\LivewirePowerGrid\Plugins\Tabs;

use PowerComponents\LivewirePowerGrid\Plugins\PluginBase;

class TabsPlugin extends PluginBase
{
    public function name(): string
    {
        return 'tabs';
    }

    public function isEnabled(): bool
    {
        return $this->component->hasTabs();
    }

    public function handlesZone(string $zone): bool
    {
        return $zone === 'header.tabs' && $this->isEnabled();
    }

    public function renderZone(string $zone): ?string
    {
        if (! $this->handlesZone($zone)) {
            return null;
        }

        return view($this->component->tabsView(), $this->component->tabsData())->render();
    }
}
