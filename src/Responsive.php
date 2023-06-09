<?php

namespace PowerComponents\LivewirePowerGrid;

final class Responsive
{
    public string $name = 'responsive';

    public const ACTIONS_COLUMN_NAME = 'actions';

    public array $fixedColumns = ['id', self::ACTIONS_COLUMN_NAME];

    public static function make(): static
    {
        return new static();
    }

    public function fixedColumns(string ...$columnNames): static
    {
        $this->fixedColumns = [...$columnNames];

        return $this;
    }
}
