<?php

namespace PowerComponents\LivewirePowerGrid\Themes\Components;

class Cols
{
    use HasProperties;

    public function div(string $div): self
    {
        $this->properties['div'] = $div;

        return $this;
    }
}
