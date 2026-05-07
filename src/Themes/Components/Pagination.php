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

    public static function make(string $view = ''): self
    {
        return new self($view);
    }

    public function view(string $view): self
    {
        $this->properties['view'] = $view;

        return $this;
    }
}
