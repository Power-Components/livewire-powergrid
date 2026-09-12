<?php

namespace PowerComponents\LivewirePowerGrid\Plugins\Flatpickr;

use Illuminate\Support\Str;
use Livewire\Attributes\On;
use PowerComponents\LivewirePowerGrid\Plugins\PluginBase;
use PowerComponents\Turbine\Support\FilterDateRange;

class FlatpickrPlugin extends PluginBase
{
    public function name(): string
    {
        return 'flatpickr';
    }

    public function isEnabled(): bool
    {
        $hasFilterInColumns = collect($this->component->columns)
            ->contains(function ($column) {
                /** @var array<int, mixed> $filters */
                $filters = data_get($column, 'filters', []);

                return collect($filters)
                    ->contains(function ($filter) {
                        $type = data_get($filter, 'type');
                        if ($type) {
                            return in_array($type, ['date_picker', 'datetime']);
                        }

                        if (is_object($filter)) {
                            $className = get_class($filter);

                            return str_contains($className, 'FilterDatePicker') || str_contains($className, 'FilterDateTimePicker');
                        }

                        return false;
                    });
            });

        $hasFilterInFilters = false;
        $hasFilterInFilters = collect($this->component->declaredFilters())
            ->contains(function ($filter) {
                $className = get_class($filter);

                return str_contains($className, 'FilterDatePicker') || str_contains($className, 'FilterDateTimePicker');
            });

        return $hasFilterInColumns || $hasFilterInFilters;
    }

    public function scripts(): array
    {
        return [__DIR__.'/index.js'];
    }

    #[On('pg:datePicker-{tableName}')]
    public function datePickerChanged(mixed ...$params): void
    {
        [$field, $selectedDates, $dateStr, $label, $type, $timezone, $dateFormat] = $params;

        if (! is_string($field) || ! is_string($type)) {
            return;
        }

        if (! is_array($selectedDates) || ! isset($selectedDates[1])) {
            return;
        }

        $dateStr = is_string($dateStr) ? $dateStr : '';
        $firstDate = is_scalar($selectedDates[0] ?? null) ? strval($selectedDates[0]) : '';
        $secondDate = is_scalar($selectedDates[1] ?? null) ? strval($selectedDates[1]) : '';
        $formatted = Str::contains($dateStr, 'to')
            ? $dateStr
            : $firstDate.' to '.$secondDate;

        $this->component->putFilterRecord(
            $field,
            $type,
            self::computeRange($type, $formatted),
        );

        /** @var string|null $label */
        $this->component->addEnabledFilters($field, $label);
        $this->component->commitFilters();
    }

    /**
     * A date draft carries what flatpickr wrote (`value.formatted`); the applied
     * record carries the range that flows into the query.
     *
     * @param  array<string, mixed>  $record
     * @return array<string, mixed>|null
     */
    public function normalizeFilterRecord(string $field, array $record): ?array
    {
        $type = $record['type'] ?? '';

        if (! in_array($type, ['date', 'datetime'], true)) {
            return $record;
        }

        $formatted = data_get($record, 'value.formatted');

        if (blank($formatted)) {
            return null;
        }

        $record['value'] = self::computeRange((string) $type, is_scalar($formatted) ? (string) $formatted : '');

        return $record;
    }

    public function onFilterCleared(string $field, string $type): void
    {
        if (in_array($type, ['date', 'datetime'], true)) {
            $this->component->dispatch('pg:clear_flatpickr::'.$this->component->tableName.':'.$field);
        }
    }

    public function onFiltersCleared(): void
    {
        $this->component->dispatch('pg:clear_all_flatpickr::'.$this->component->tableName);
    }

    public function onFiltersRestored(): void
    {
        $this->component->dispatch('pg:restore_flatpickr::'.$this->component->tableName);
    }

    /**
     * @return array{start: string, end: string, formatted: string}
     */
    public static function computeRange(string $type, string $formatted): array
    {
        return FilterDateRange::compute($type, $formatted);
    }
}
