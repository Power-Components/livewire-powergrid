<?php

namespace PowerComponents\LivewirePowerGrid;

final class Responsive
{
    public string $name = 'responsive';

    public const ACTIONS_COLUMN_NAME = 'actions';

    public const CHECKBOX_COLUMN_NAME = 'checkbox';

    public array $fixedColumns = ['id', self::CHECKBOX_COLUMN_NAME, self::ACTIONS_COLUMN_NAME];

    public array $sortOrder = [];

    public static function make(): static
    {
        return new static();
    }

    public function fixedColumns(string ...$columnNames): static
    {
        $this->fixedColumns = [...$columnNames];

        return $this;
    }

    public function sortOrder(string|array ...$sortOrder): static
    {
        if (is_array(data_get($sortOrder, '0'))) {
            $sortOrder = $sortOrder[0];
        }

        foreach ($sortOrder as $key => $value) {
            if (is_int($key)) {
                $this->sortOrder[$value] = $key + 1;

                continue;
            }

            $this->sortOrder[$key] = $value;
        }

        return $this;
    }
}
