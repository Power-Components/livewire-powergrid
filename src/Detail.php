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

    public bool $collapseOthers = false;

    public static function make(): self
    {
        return new Detail();
    }

    public function view(string $view): Detail
    {
        $this->view      = $view;

        return $this;
    }

    /** @deprecated - use params instead of options, it will deprecate in version 4 */
    public function options(array $options = []): Detail
    {
        $this->options   = $options;

        return $this;
    }

    public function params(array $params = []): Detail
    {
        $this->options   = $params;

        return $this;
    }

    public function showCollapseIcon(string $viewIcon = ''): Detail
    {
        $this->showCollapseIcon = true;
        $this->viewIcon         = $viewIcon;

        return $this;
    }

    public function collapseOthers(): Detail
    {
        $this->collapseOthers   = true;

        return $this;
    }
}
