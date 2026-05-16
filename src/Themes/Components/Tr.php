<?php

namespace PowerComponents\LivewirePowerGrid\Themes\Components;

class Tr
{
    use HasProperties;

    public function base(string $base): self
    {
        $this->properties['base'] = $base;

        return $this;
    }

    public function responsive(string $responsive): self
    {
        $this->properties['responsive'] = $responsive;

        return $this;
    }

    public function responsiveToggleIcon(string $responsiveToggleIcon): self
    {
        $this->properties['responsiveToggleIcon'] = $responsiveToggleIcon;

        return $this;
    }
}
