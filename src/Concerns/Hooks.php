<?php

namespace PowerComponents\LivewirePowerGrid\Concerns;

use Illuminate\Support\Collection;

trait Hooks
{
    public function onUpdatedEditable(string|int $id, string $field, string $value): void {}

    public function onUpdatedToggleable(string $id, string $field, string $value): void {}

    public function afterChangedMultiSelectFilter(string $field, array $values): void {}

    public function afterChangedSelectFilter(string $field, string $label, mixed $value): void {}

    public function afterChangedInputTextFilter(string $field, string $label, string $value): void {}

    public function afterChangedBooleanFilter(string $field, string $label, string $value): void {}

    public function afterChangedNumberStartFilter(string $field, string $label, string|false $value): void {}

    public function afterChangedNumberEndFilter(string $field, string $label, string|false $value): void {}

    public function transformRows(Collection $rows): Collection
    {
        return $rows;
    }

    public function transformQuery(mixed $query): mixed
    {
        return $query;
    }

    public function transformActions(array $actionsByRow, Collection $rows): array
    {
        return $actionsByRow;
    }
}
