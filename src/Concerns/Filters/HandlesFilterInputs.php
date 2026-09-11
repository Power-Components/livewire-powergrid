<?php

namespace PowerComponents\LivewirePowerGrid\Concerns\Filters;

use Exception;
use Livewire\Attributes\On;
use PowerComponents\LivewirePowerGrid\Support\FilterKey;

trait HandlesFilterInputs
{
    /**
     * Livewire wrote `$filters.*` (inline `wire:model.live`). Commit the bag.
     *
     * @throws Exception
     */
    protected function filtersUpdated(string $name): void
    {
        if (str_starts_with($name, 'filters.')) {
            $this->notifyFilterPropertyChanged(substr($name, strlen('filters.')));
        }

        $this->commitFilters();
    }

    /**
     * @param  list<string>  $values
     *
     * @throws Exception
     */
    #[On('pg:multiSelect-{tableName}')]
    public function multiSelectChanged(
        string $field,
        string $label,
        array $values,
    ): void {
        $this->putFilterRecord($field, 'multi_select', $values, label: $label);
        $this->notifyFilterChanged($field);

        if (count($values) === 0) {
            $this->dispatch('pg:clear_multi_select::'.$this->tableName.':'.$field);
            $this->clearFilter($field);

            return;
        }

        $this->commitFilters();
    }

    /**
     * @deprecated Write `$this->filters[$field]['value']` (or putFilterRecord) and commitFilters().
     *
     * @throws Exception
     */
    public function filterSelect(string $field, string $label): void
    {
        $this->addEnabledFilters($field, $label);
        $this->notifyFilterChanged($field);

        if (blank($this->filterRecordValue($field))) {
            $this->clearFilter($field);

            return;
        }

        $this->commitFilters();
    }

    /**
     * @deprecated Write `$this->filters[$field]['value']['start']` (or putNumberFilterValue) and commitFilters().
     *
     * @param  array<string, mixed>  $params
     *
     * @throws Exception
     */
    public function filterNumberStart(string $field, array $params, string $value): void
    {
        /** @var string $title */
        $title = data_get($params, 'title');

        $this->putNumberFilterValue($field, 'start', $value, $title);
        $this->notifyFilterChanged($field, 'start');
        $this->commitFilters();
    }

    /**
     * @deprecated Write `$this->filters[$field]['value']['end']` (or putNumberFilterValue) and commitFilters().
     *
     * @param  array<string, mixed>  $params
     *
     * @throws Exception
     */
    public function filterNumberEnd(string $field, array $params, string $value): void
    {
        /** @var string $title */
        $title = data_get($params, 'title');

        $this->putNumberFilterValue($field, 'end', $value, $title);
        $this->notifyFilterChanged($field, 'end');
        $this->commitFilters();
    }

    /**
     * @deprecated Write `$this->filters[$field]['value']` (or putFilterRecord) and commitFilters().
     *
     * @throws Exception
     */
    public function filterBoolean(string $field, string $value, string $label): void
    {
        $this->putFilterRecord($field, 'boolean', $value, label: $label);
        $this->notifyFilterChanged($field);

        if ($value === 'all') {
            $this->clearFilter($field);

            return;
        }

        $this->commitFilters();
    }

    /**
     * @deprecated Write `$this->filters[$field]['value']` (or putFilterRecord) and commitFilters().
     *
     * @throws Exception
     */
    public function filterInputText(string $field, string $value, string $label = ''): void
    {
        $this->putFilterRecord($field, 'input_text', $value, label: $label);
        $this->notifyFilterChanged($field);

        if (blank($value)) {
            $this->clearFilter($field);

            return;
        }

        $this->commitFilters();
    }

    /**
     * @deprecated Write `$this->filters[$field]['op']` (or putFilterRecord) and commitFilters().
     *
     * @throws Exception
     */
    public function filterInputTextOptions(string $field, string $value, string $label = ''): void
    {
        if (! $this->isDeclaredFilterField($field)) {
            return;
        }

        $this->putFilterRecord($field, 'input_text', $this->filterRecordValue($field), $value, $label);
        $this->notifyFilterChanged($field, skipLegacy: true);

        if (blank($value)) {
            $this->clearFilter($field);

            return;
        }

        $this->commitFilters();
    }

    protected function notifyFilterPropertyChanged(string $path): void
    {
        $parsed = $this->parseFilterPath($path);

        if ($parsed === null) {
            return;
        }

        [$field, $suffix, $bound] = $parsed;

        $this->notifyFilterChanged($field, $bound, skipLegacy: $suffix === 'op');
    }

    protected function notifyFilterChanged(string $field, ?string $bound = null, bool $skipLegacy = false): void
    {
        $record = $this->filterRecord($field) ?? [];

        if (! isset($record['type']) || ! is_string($record['type']) || $record['type'] === '') {
            $record['type'] = $this->declaredFilterType($field);
        }

        $this->afterFilterChanged($field, $record);

        if (! $skipLegacy) {
            $this->dispatchLegacyFilterChanged($field, $record, $bound);
        }
    }

    /**
     * @param  array<string, mixed>  $record
     */
    protected function dispatchLegacyFilterChanged(string $field, array $record, ?string $bound): void
    {
        $label = is_string($record['label'] ?? null) ? $record['label'] : ($this->filterPillLabels()[$field] ?? $field);
        $type = is_string($record['type'] ?? null) ? $record['type'] : '';

        match ($type) {
            'input_text' => $this->afterChangedInputTextFilter($field, $label, $this->filterValueAsString($field)),
            'boolean' => $this->afterChangedBooleanFilter($field, $label, $this->filterValueAsString($field)),
            'select' => $this->afterChangedSelectFilter($field, $label, $this->filterRecordValue($field)),
            'multi_select' => $this->afterChangedMultiSelectFilter($field, $this->filterValueAsList($field)),
            'number' => $bound === 'end'
                ? $this->afterChangedNumberEndFilter($field, $label, $this->numberBoundAsString($field, 'end'))
                : $this->afterChangedNumberStartFilter($field, $label, $this->numberBoundAsString($field, 'start')),
            default => null,
        };
    }

    /**
     * @return array{0: string, 1: string, 2: string|null}|null
     */
    protected function parseFilterPath(string $path): ?array
    {
        $decoded = FilterKey::decode($path);

        if (str_ends_with($decoded, '.value.start')) {
            return [substr($decoded, 0, -strlen('.value.start')), 'value', 'start'];
        }

        if (str_ends_with($decoded, '.value.end')) {
            return [substr($decoded, 0, -strlen('.value.end')), 'value', 'end'];
        }

        if (str_ends_with($decoded, '.value')) {
            $field = substr($decoded, 0, -strlen('.value'));

            return $field === '' ? null : [$field, 'value', null];
        }

        if (str_ends_with($decoded, '.op')) {
            $field = substr($decoded, 0, -strlen('.op'));

            return $field === '' ? null : [$field, 'op', null];
        }

        return null;
    }

    protected function declaredFilterType(string $field): string
    {
        foreach ($this->declaredFilters() as $filter) {
            $declaredField = data_get($filter, 'field');
            $declaredColumn = data_get($filter, 'column');

            if ($declaredField === $field || $declaredColumn === $field) {
                $key = data_get($filter, 'key');

                return is_string($key) ? $key : '';
            }
        }

        return '';
    }

    protected function filterRecordValue(string $field): mixed
    {
        $record = $this->filterRecord($field);

        return is_array($record) ? ($record['value'] ?? null) : null;
    }

    protected function filterValueAsString(string $field): string
    {
        $value = $this->filterRecordValue($field);

        return is_scalar($value) ? strval($value) : '';
    }

    /** @return list<string> */
    protected function filterValueAsList(string $field): array
    {
        $value = $this->filterRecordValue($field);

        if (! is_array($value)) {
            return [];
        }

        $items = [];

        foreach ($value as $item) {
            if (is_scalar($item)) {
                $items[] = strval($item);
            }
        }

        return $items;
    }

    protected function numberBoundAsString(string $field, string $bound): string
    {
        $value = $this->filterRecordValue($field);
        $item = is_array($value) ? ($value[$bound] ?? null) : null;

        return is_scalar($item) ? strval($item) : '';
    }
}
