<?php

namespace PowerComponents\LivewirePowerGrid\Concerns\Filters;

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

    public function addEnabledFilters(string $field, ?string $label, bool $disabled = false): void
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

        $this->enabledFilters = [];
        $this->rebuildEnabledFilters($labels);
        $this->syncFilterBuilderPills();
    }

    /**
     * @param  array<string, string>  $labels
     */
    protected function rebuildEnabledFilters(array $labels = []): void
    {
        $seen = [];

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

            $this->enabledFilters[] = $pill;
        }
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
