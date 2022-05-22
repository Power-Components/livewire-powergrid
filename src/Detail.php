<?php

namespace PowerComponents\LivewirePowerGrid;

final class Detail
{
    public string $name = 'detail';

    public string $view = '';

    public array $options = [];

    public array $state = [];

    public bool $showCollapseIcon = false;

    public string $viewIcon = '';

    public static function make(): self
    {
        return new Detail();
    }

    public function view(string $view): Detail
    {
        $this->view      = $view;

        return $this;
    }

    public function options(array $options = []): Detail
    {
        $this->options   = $options;

        return $this;
    }

    public function showCollapseIcon(string $viewIcon = ''): Detail
    {
        $this->showCollapseIcon = true;
        $this->viewIcon         = $viewIcon;

        return $this;
    }
}
