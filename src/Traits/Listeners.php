<?php

namespace PowerComponents\LivewirePowerGrid\Traits;

/** @codeCoverageIgnore  */
trait Listeners
{
    public function inputTextChanged(string|int $id, string $field, string $value): void
    {
        $this->{$field}[$id] = $value;

        $this->onUpdatedEditable($id, $field, $value);

        $this->dispatch('pg:editable-close-' . $id);
    }

    public function toggleableChanged(array $payload = []): void
    {
        $id    = $payload['id'];
        $field = $payload['field'];

        $this->onUpdatedToggleable($id, $field, $payload['value']);
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

    public function afterChangedNumberStartFilter(string $field, string $label, string $value): void
    {
    }

    public function afterChangedNumberEndFilter(string $field, string $label, string $value): void
    {
    }
}
