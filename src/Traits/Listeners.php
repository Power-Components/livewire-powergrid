<?php

namespace PowerComponents\LivewirePowerGrid\Traits;

use Livewire\Attributes\On;

/** @codeCoverageIgnore  */
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

    public function onUpdatedEditable(string|int $id, string $field, string $value): void
    {
    }

    public function onUpdatedToggleable(string $id, string $field, string $value): void
    {
    }

    public function afterChangedMultiSelectFilter(string $field, array $values): void
    {
    }

    public function afterChangedSelectFilter(string $field, string $label, mixed $value): void
    {
    }

    public function afterChangedInputTextFilter(string $field, string $label, string $value): void
    {
    }

    public function afterChangedBooleanFilter(string $field, string $label, string $value): void
    {
    }

    public function afterChangedNumberStartFilter(string $field, string $label, string|false $value): void
    {
    }

    public function afterChangedNumberEndFilter(string $field, string $label, string|false $value): void
    {
    }
}
