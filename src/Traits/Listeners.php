<?php

namespace PowerComponents\LivewirePowerGrid\Traits;

use Exception;

/** @codeCoverageIgnore  */
trait Listeners
{
    /**
     * @param array $data
     * @return void
     * @throws Exception
     */
    public function inputTextChanged(array $data = []): void
    {
        $id         = $data['id'];
        $field      = $data['field'];

        $this->{$field}[$id] = $data['value'];

        $this->onUpdatedEditable($id, $field, $data['value']);

        $this->dispatchBrowserEvent('pg:editable-close-' . $id);
    }

    /**
     * @param array $data
     * @return void
     * @throws Exception
     */
    public function toggleableChanged(array $data = []): void
    {
        $id         = $data['id'];
        $field      = $data['field'];

        $this->onUpdatedToggleable($id, $field, $data['value']);
    }

    public function onUpdatedEditable(string $id, string $field, string $value): void
    {
    }

    public function onUpdatedToggleable(string $id, string $field, string $value): void
    {
    }

    public function onUpdatedMultiSelect(string $field, array $values): void
    {
    }
}
