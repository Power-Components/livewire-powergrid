<?php

namespace PowerComponents\LivewirePowerGrid;

final class Responsive
{
    public string $name = 'responsive';

    public array $fixedColumns = ['id', 'actions'];

    public bool $showExpand = false;

    public static function make(): static
    {
        return new static();
    }

    public function fixedColumns(string ...$columnNames): static
    {
        $this->fixedColumns = [...$columnNames];

        return $this;
    }

    public function showExpand(): static
    {
        $this->showExpand = true;

        return $this;
    }
}
