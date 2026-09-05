<?php

namespace PowerComponents\LivewirePowerGrid\Themes\Components;

class Pagination
{
    use HasProperties;

    public function __construct(string $view = '')
    {
        if (filled($view)) {
            $this->properties['view'] = $view;
        }
    }

    public function view(string $view): self
    {
        $this->properties['view'] = $view;

        return $this;
    }
}
