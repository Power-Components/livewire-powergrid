<?php

namespace PowerComponents\LivewirePowerGrid\Concerns\Filters;

use Closure;
use PowerComponents\Turbine\Support\FilterBag;

trait DerivesFilterPills
{
    public function activeFilterCount(): int
    {
        return collect($this->enabledFilters)
            ->reject(fn ($filter) => ($filter['source'] ?? null) === 'filterBuilder')
            ->map(fn ($filter) => is_string($field = data_get($filter, 'field')) ? $field : '')
            ->filter(fn ($field) => $field !== '')
            ->unique()
            ->count();
    }

    public function addEnabledFilters(string $field, ?string $label): void
    {
        $key = $this->filterBagKey($field);

        if (! filled($label) || $key === '' || ! is_array($this->filters[$key] ?? null)) {
            return;
        }

        $this->filters[$key]['label'] = $label;
    }

    protected function syncFilterPills(): void
    {
        $labels = $this->filterPillLabels();
        $previous = collect($this->enabledFilters)->keyBy('field')->all();

        $this->enabledFilters = [];
        $this->rebuildEnabledFilters($labels, $previous);
        $this->syncFilterBuilderPills();
    }

    /**
     * @param  array<string, string>  $labels
     * @param  array<array-key, mixed>  $previous  Pills from the last sync, keyed by field.
     */
    protected function rebuildEnabledFilters(array $labels = [], array $previous = []): void
    {
        $seen = [];
        $showValue = $this->showFilterPillValue();

        foreach ($this->declaredFilters() as $filter) {
            $key = $this->filterBagKey($this->declaredFilterField($filter));
            $record = $key === '' ? null : $this->filterRecord($key);

            if ($record === null || ! FilterBag::isActive($record) || isset($seen[$key])) {
                continue;
            }

            $seen[$key] = true;

            $recordLabel = $record['label'] ?? null;
            $pill = [
                'field' => $key,
                'label' => (is_string($recordLabel) && $recordLabel !== '') ? $recordLabel : ($labels[$key] ?? $key),
            ];

            if (FilterBag::isValuelessOperator($record['op'] ?? null)) {
                $pill['disabled'] = true;
            }

            if ($showValue) {
                // Resolve only when the record changed: select labels may run a dataSource query.
                $valueKey = md5((string) json_encode([$record['type'] ?? null, $record['value'] ?? null, $record['op'] ?? null]));
                $cached = $previous[$key] ?? null;
                $value = is_array($cached) && ($cached['valueKey'] ?? null) === $valueKey
                    ? $this->pillText($cached['value'] ?? '')
                    : $this->filterPillValue($filter, $record);

                $pill['valueKey'] = $valueKey;

                if ($value !== '') {
                    $pill['value'] = $value;
                }
            }

            $this->enabledFilters[] = $pill;
        }
    }

    /**
     * Human-readable text for an active filter record, shown in its pill.
     *
     * @param  array<string, mixed>  $record
     */
    protected function filterPillValue(mixed $filter, array $record): string
    {
        $value = $record['value'] ?? null;
        $op = $record['op'] ?? null;
        $op = is_array($op) ? collect($op)->values()->first() : $op;

        if (FilterBag::isValuelessOperator($op)) {
            return $this->pillText(trans('livewire-powergrid::datatable.input_text_options.'.$this->pillText($op)));
        }

        return match ($record['type']) {
            'boolean' => $this->pillText(data_get($filter, filter_var($value, FILTER_VALIDATE_BOOLEAN) ? 'trueLabel' : 'falseLabel')),
            'select', 'multi_select' => $this->filterOptionLabels($filter, (array) $value),
            'number' => $this->filterRangeText(data_get($value, 'start'), data_get($value, 'end')),
            'date', 'datetime' => $this->pillText(data_get($value, 'formatted'))
                ?: $this->filterRangeText(data_get($value, 'start'), data_get($value, 'end')),
            default => $this->pillText($value),
        };
    }

    /**
     * Map selected option values to their option labels (falling back to the raw value).
     *
     * @param  array<array-key, mixed>  $values
     */
    protected function filterOptionLabels(mixed $filter, array $values): string
    {
        if (is_object($filter) && method_exists($filter, 'execute')) {
            $filter = (clone $filter)->execute();
        }

        $computed = data_get($filter, 'computedDatasource');
        $options = is_string($computed) && $computed !== ''
            ? $this->{$computed}
            : data_get($filter, 'dataSource');

        if ($options instanceof Closure) {
            $options = $options($this->filterDependsValues((array) data_get($filter, 'depends')));
        }

        $optionValue = $this->pillText(data_get($filter, 'optionValue'));
        $optionLabel = $this->pillText(data_get($filter, 'optionLabel'));
        $labels = [];

        foreach (is_iterable($options) ? $options : [] as $option) {
            $labels[$this->pillText(data_get($option, $optionValue))] = $this->pillText(data_get($option, $optionLabel));
        }

        return collect($values)
            ->map(fn ($value) => $this->pillText($value))
            ->filter(fn (string $value) => $value !== '')
            ->map(fn (string $value) => ($labels[$value] ?? '') ?: $value)
            ->implode(', ');
    }

    protected function filterRangeText(mixed $start, mixed $end): string
    {
        $start = $this->pillText($start);
        $end = $this->pillText($end);

        return match (true) {
            $start !== '' && $end !== '' => "{$start} - {$end}",
            $start !== '' => "≥ {$start}",
            $end !== '' => "≤ {$end}",
            default => '',
        };
    }

    private function pillText(mixed $value): string
    {
        return is_scalar($value) ? (string) $value : '';
    }

    /**
     * @return array<string, string>
     */
    protected function filterPillLabels(): array
    {
        $labels = [];

        foreach ($this->columns as $column) {
            $title = data_get($column, 'title');

            if (! is_scalar($title) || (string) $title === '') {
                continue;
            }

            foreach ([data_get($column, 'field'), data_get($column, 'dataField')] as $field) {
                if (is_string($field) && ($key = $this->filterBagKey($field) ?: $field) !== '') {
                    $labels[$key] = (string) $title;
                }
            }
        }

        $stamped = [];

        foreach ($this->filters as $field => $record) {
            $stamped[] = [$field, is_array($record) ? ($record['label'] ?? null) : null];
        }

        foreach ($this->enabledFilters as $pill) {
            if (($pill['source'] ?? null) !== 'filterBuilder') {
                $stamped[] = [$pill['field'] ?? null, $pill['label'] ?? null];
            }
        }

        foreach ($stamped as [$field, $label]) {
            if (is_string($field) && $field !== '' && is_string($label) && $label !== '') {
                $labels[$field] = $label;
            }
        }

        return $labels;
    }
}
