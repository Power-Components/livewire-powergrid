<?php

namespace PowerComponents\LivewirePowerGrid\Plugins\Flatpickr;

use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Support\{Carbon, Str};
use Livewire\Attributes\On;
use PowerComponents\LivewirePowerGrid\Plugins\PluginBase;

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
        if (method_exists($this->component, 'filters')) {
            $hasFilterInFilters = collect($this->component->filters())
                ->contains(function ($filter) {
                    $className = get_class($filter);

                    return str_contains($className, 'FilterDatePicker') || str_contains($className, 'FilterDateTimePicker');
                });
        }

        return $hasFilterInColumns || $hasFilterInFilters;
    }

    #[On('pg:datePicker-{tableName}')]
    public function datePickerChanged(...$params): void
    {
        [$field, $selectedDates, $dateStr, $label, $type, $timezone, $dateFormat] = $params;

        ds(get_defined_vars());

        if (! isset($selectedDates[1])) {
            return;
        }

        $this->component->resetPage();

        [$startRaw, $endRaw] = Str::contains($dateStr, 'to')
            ? explode(' to ', $dateStr)
            : [strval($selectedDates[0]), strval($selectedDates[1])];

        $appTimezone = config('app.timezone');
        $isDatetime = $type === 'datetime';
        $hasTime = str_contains($dateFormat, 'H');

        $makeDate = function ($dateStr) use ($hasTime, $appTimezone) {
            try {
                $date = Carbon::parse($dateStr, $appTimezone);
            } catch (InvalidFormatException) {
                return now($appTimezone);
            }

            if (! $hasTime) {
                $date->setTime(0, 0, 0);
            }

            return $date->setTimezone($appTimezone);
        };

        $startDate = $makeDate($startRaw);
        $endDate = $makeDate($endRaw);

        if ($isDatetime && $endDate->isStartOfDay()) {
            $endDate->endOfDay();
        } elseif (! $isDatetime) {
            $endDate->endOfDay();
        }

        $this->component->addEnabledFilters($field, $label);

        $filters = $this->component->filters;
        $filters[$type][$field] = [
            'start' => $startDate->toString(),
            'end' => $endDate->toString(),
            'formatted' => $dateStr,
        ];
        $this->component->filters = $filters;

        $this->component->persistState('filters');

        $this->component->renderOutsideFiltersPartial();
    }
}
