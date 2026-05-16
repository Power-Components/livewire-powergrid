<?php

namespace PowerComponents\LivewirePowerGrid\Themes\Components;

class Td
{
    use HasProperties;

    public function base(string $base): self
    {
        $this->properties['base'] = $base;

        return $this;
    }

    public function actionsWrapper(string $actionsWrapper): self
    {
        $this->properties['actionsWrapper'] = $actionsWrapper;

        return $this;
    }
}
