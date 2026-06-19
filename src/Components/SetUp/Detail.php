<?php

namespace PowerComponents\LivewirePowerGrid\Components\SetUp;

use Livewire\Wireable;

final class Detail implements Wireable
{
    public string $name = 'detail';

    public string $view = '';

    /** @var array<string, mixed> */
    public array $options = [];

    /** @var array<string, mixed> */
    public array $state = [];

    public bool $showCollapseIcon = false;

    public string $viewIcon = '';

    public bool $collapseOthers = false;

    public function view(string $view): Detail
    {
        $this->view = $view;

        return $this;
    }

    /**
     * @deprecated - use params instead of options, it will deprecate in version 4
     *
     * @param  array<string, mixed>  $options
     */
    public function options(array $options = []): Detail
    {
        $this->options = $options;

        return $this;
    }

    /** @param  array<string, mixed>  $params */
    public function params(array $params = []): Detail
    {
        $this->options = $params;

        return $this;
    }

    public function showCollapseIcon(string $viewIcon = ''): Detail
    {
        $this->showCollapseIcon = true;
        $this->viewIcon = $viewIcon;

        return $this;
    }

    public function collapseOthers(): Detail
    {
        $this->collapseOthers = true;

        return $this;
    }

    /** @return array<string, mixed> */
    public function toLivewire(): array
    {
        return (array) $this;
    }

    public static function fromLivewire($value)
    {
        return $value;
    }
}
