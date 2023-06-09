<?php

namespace PowerComponents\LivewirePowerGrid;

final class Responsive
{
    public string $name = 'responsive';

    public const ACTIONS_COLUMN_NAME = 'actions';

    public const CHECKBOX_COLUMN_NAME = 'checkbox';

    public array $fixedColumns = ['id', self::CHECKBOX_COLUMN_NAME, self::ACTIONS_COLUMN_NAME];

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
