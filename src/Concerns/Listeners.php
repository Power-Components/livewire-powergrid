<?php

namespace PowerComponents\LivewirePowerGrid\Concerns;

use Livewire\Attributes\On;

/** @codeCoverageIgnore */
trait Listeners
{
    #[On('pg:editable-{tableName}')]
    public function inputTextChanged(string|int $id, string $field, string $value): void
    {
        data_set($this, "$field.{$id}", $value);

        $this->onUpdatedEditable($id, $field, $value);

        $this->dispatch('pg:editable-close-' . $id);
    }

    #[On('pg:toggleable-{tableName}')]
    public function toggleableChanged(string $id, string $field, string $value): void
    {
        $this->onUpdatedToggleable($id, $field, $value);
    }

    #[On('pg:toggleColumn-{tableName}')]
    public function toggleColumn(string $field): void
    {
        $this->visibleColumns = $this->visibleColumns->map(function (\stdClass | array $column) use ($field) {
            if (is_object($column) && $column->field === $field) {
                $column->hidden = !$column->hidden;
            }

            if (is_array($column) && $column['field'] === $field) {
                $column['hidden'] = !$column['hidden'];
            }

            return $column;
        });

        $this->persistState('columns');
    }

    #[On('pg:eventRefresh-{tableName}')]
    public function refresh(): void
    {
        if (($this->total > 0) && ($this->totalCurrentPage - 1) === 0) {
            $this->previousPage();

            return;
        }

        if ($this->hasLazyEnabled) {
            $this->additionalCacheKey = uniqid();
        }

        $this->dispatch('$commit')->self();
    }
}
