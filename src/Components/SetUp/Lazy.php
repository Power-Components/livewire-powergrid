<?php

namespace PowerComponents\LivewirePowerGrid\Components\SetUp;

use Livewire\Wireable;

final class Lazy implements Wireable
{
    public string $name = 'lazy';

    public bool $active;

    public int $rowsPerChildren = 25;

    public int $items = 0;

    public string $dispatchAfterToggleDetail;

    public function rowsPerChildren(int $qty): static
    {
        $this->rowsPerChildren = $qty;

        return $this;
    }

    public function inactive(): static
    {
        $this->active = false;

        return $this;
    }

    public function dispatchAfterToggleDetail(string $event): static
    {
        $this->dispatchAfterToggleDetail = $event;

        return $this;
    }

    public function toLivewire(): array
    {
        return (array) $this;
    }

    public static function fromLivewire($value)
    {
        return $value;
    }
}
