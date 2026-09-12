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

    /**
     * Called after a filter value changes (inline `wire:model.live`, programmatic write, or Apply).
     *
     * @param  array<string, mixed>  $record
     */
    public function afterFilterChanged(string $field, array $record): void {}

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
        $query = $this->applyActiveTabScope($query);

        $definition = $this->definition();

        return $definition !== null ? $definition->transformQuery($query) : $query;
    }

    public function tabQuery(string $tab, mixed $query): mixed
    {
        return $query;
    }

    public function tabBadge(string $tab): int|string|null
    {
        return null;
    }

    /**
     * @param  array{match: string, rows: list<array<string, mixed>>}  $conditions
     */
    public function beforeFilterBuilderApply(mixed $query, array $conditions): mixed
    {
        return $query;
    }

    /**
     * @param  array{match: string, rows: list<array<string, mixed>>}  $conditions
     */
    public function validateFilterBuilder(array $conditions): void {}

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
