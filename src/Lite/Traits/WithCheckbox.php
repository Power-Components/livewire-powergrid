<?php

namespace PowerComponents\LivewirePowerGrid\Lite\Traits;

trait WithCheckbox
{
    public bool $checkboxAll = false;

    public array $checkboxValues = [];

    public string $checkboxAttribute = 'id';

    public function updatedCheckboxAll(bool $value): void
    {
        if ($value === false) {
            $this->checkboxValues = [];

            return;
        }

        if (method_exists($this, 'getAllCheckboxValues')) {
            $this->checkboxValues = array_map('strval', $this->getAllCheckboxValues());
        }

        $this->afterCheckboxChanged();
    }

    public function updatedCheckboxValues(): void
    {
        $this->checkboxAll = false;
        $this->afterCheckboxChanged();
    }

    public function isChecked(string|int $value): bool
    {
        return in_array((string) $value, $this->checkboxValues, true);
    }

    public function getSelected(): array
    {
        return $this->checkboxValues;
    }

    public function clearSelected(): void
    {
        $this->checkboxValues = [];
        $this->checkboxAll = false;
        $this->afterCheckboxChanged();
    }

    private function afterCheckboxChanged(): void
    {
        if (method_exists($this, 'persistState')) {
            $this->persistState('checkbox');
        }
    }
}
