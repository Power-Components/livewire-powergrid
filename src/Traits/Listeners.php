<?php

namespace PowerComponents\LivewirePowerGrid\Traits;

use Exception;

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

        $this->fillData();
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

        $this->{$field}[$id] = $data['value'];

        $this->onUpdatedToggleable($id, $field, $data['value']);

        $this->fillData();
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
