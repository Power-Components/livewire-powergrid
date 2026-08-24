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
        $hasFilterInFilters = collect($this->component->filters())
            ->contains(function ($filter) {
                $className = get_class($filter);

                return str_contains($className, 'FilterDatePicker') || str_contains($className, 'FilterDateTimePicker');
            });

        return $hasFilterInColumns || $hasFilterInFilters;
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

        $this->component->resetPage();

        $dateStr = is_string($dateStr) ? $dateStr : '';
        $firstDate = is_scalar($selectedDates[0] ?? null) ? strval($selectedDates[0]) : '';
        $secondDate = is_scalar($selectedDates[1] ?? null) ? strval($selectedDates[1]) : '';
        $formatted = Str::contains($dateStr, 'to')
            ? $dateStr
            : $firstDate.' to '.$secondDate;

        /** @var string|null $label */
        $this->component->addEnabledFilters($field, $label);

        $filters = $this->component->filters;
        $filters[$type][$field] = self::computeRange($type, $formatted);
        $this->component->filters = $filters;

        $this->component->persistState('filters');

        $this->component->renderOutsideFiltersPartial();
    }

    /**
     * @return array{start: string, end: string, formatted: string}
     */
    public static function computeRange(string $type, string $formatted): array
    {
        return FilterDateRange::compute($type, $formatted);
    }
}
