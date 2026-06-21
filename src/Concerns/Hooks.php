<?php

namespace PowerComponents\LivewirePowerGrid\Concerns;

use Illuminate\Support\Collection;

trait Hooks
{
    /**
     * Generic plugin hook. Called by plugins when they process updates.
     * Override in your component to handle plugin events.
     *
     * @param  string  $plugin  Plugin name (e.g., 'editable', 'toggleable')
     * @param  string  $event  Event name (e.g., 'updated')
     * @param  array<string, mixed>  $params  Event parameters [id, field, value, ...]
     */
    public function onPluginUpdated(string $plugin, string $event, array $params): void {}

    /**
     * @deprecated Use onPluginUpdated('editable', 'updated', [...]) instead
     */
    public function onUpdatedEditable(string|int $id, string $field, string $value): void
    {
        $this->onPluginUpdated('editable', 'updated', compact('id', 'field', 'value'));
    }

    /**
     * @deprecated Use onPluginUpdated('toggleable', 'updated', [...]) instead
     */
    public function onUpdatedToggleable(string $id, string $field, string $value): void
    {
        $this->onPluginUpdated('toggleable', 'updated', compact('id', 'field', 'value'));
    }

    /** @param  list<string>  $values */
    public function afterChangedMultiSelectFilter(string $field, array $values): void {}

    public function afterChangedSelectFilter(string $field, string $label, mixed $value): void {}

    public function afterChangedInputTextFilter(string $field, string $label, string $value): void {}

    public function afterChangedBooleanFilter(string $field, string $label, string $value): void {}

    public function afterChangedNumberStartFilter(string $field, string $label, string|false $value): void {}

    public function afterChangedNumberEndFilter(string $field, string $label, string|false $value): void {}

    /**
     * @param  Collection<int, mixed>  $rows
     * @return Collection<int, mixed>
     */
    public function transformRows(Collection $rows): Collection
    {
        return $rows;
    }

    public function transformQuery(mixed $query): mixed
    {
        return $query;
    }

    /**
     * @param  array<int|string, list<array<string, mixed>>>  $actionsByRow
     * @param  Collection<int, mixed>  $rows
     * @return array<int|string, list<array<string, mixed>>>
     */
    public function transformActions(array $actionsByRow, Collection $rows): array
    {
        return $actionsByRow;
    }
}
