<?php

namespace PowerComponents\LivewirePowerGrid\Concerns\Filters;

use Exception;
use PowerComponents\LivewirePowerGrid\Plugins\Flatpickr\FlatpickrPlugin;
use PowerComponents\LivewirePowerGrid\Support\FilterKey;
use PowerComponents\Turbine\Components\Filters\FilterManager;
use PowerComponents\Turbine\Support\FilterBag;

trait ManagesFilterState
{
    /** @param array<string, mixed> $target */
    protected function setInFilters(array &$target, string $key, mixed $value): void
    {
        /** @phpstan-ignore parameterByRef.type */
        data_set($target, $key, $value);
    }

    /**
     * Programmatic write: `$filters[$field] = ['type', 'value', 'op'?, 'label'?]`.
     * Call commitFilters() afterwards (or let Livewire `updated()` do it).
     */
    public function putFilterRecord(string $field, string $type, mixed $value, mixed $op = null, ?string $label = null): void
    {
        $record = is_array($this->filters[$field] ?? null) ? $this->filters[$field] : [];
        $record['type'] = FilterBag::normalizeType($type);
        $record['value'] = $value;

        if ($op !== null) {
            $record['op'] = $op;
        }

        if (filled($label)) {
            $record['label'] = $label;
        }

        $this->filters[$field] = $record;
    }

    protected function putNumberFilterValue(string $field, string $bound, mixed $value, ?string $label = null): void
    {
        $record = is_array($this->filters[$field] ?? null) ? $this->filters[$field] : [];
        $range = is_array($record['value'] ?? null) ? $record['value'] : [];
        $range[$bound] = $value;
        $record['type'] = 'number';
        $record['value'] = $range;

        if (filled($label)) {
            $record['label'] = $label;
        }

        $this->filters[$field] = $record;
    }

    /**
     * Prune blanks, derive pills, persist, and refresh filter UI.
     *
     * @throws Exception
     */
    public function commitFilters(bool $resetPage = true): void
    {
        $this->canonicalizeFilters();
        $this->draftFilters = FilterKey::encodeDraft($this->filters);
        $this->syncFilterPills();

        if ($resetPage) {
            $this->resetPage();
        }

        $this->persistState('filters');

        if ($this->filterPanelLoaded) {
            $this->renderFilterFieldsPartial();
        }

        $this->renderFilterPanelPartial();
        $this->refreshTabsPartial();
    }

    protected function applyDefaultFilters(): void
    {
        $this->filters = FilterBag::hydrate(FilterKey::decodeDraft($this->filters));

        $filterManager = new FilterManager();
        $applied = $filterManager->applyDefaults(
            declaredFilters: $this->declaredFilters(),
            /** @phpstan-ignore assign.propertyType */
            filters: $this->filters,
        );

        $this->canonicalizeFilters(prune: false);
        $this->syncFilterPills();
        $this->draftFilters = FilterKey::encodeDraft($this->filters);

        if ($applied) {
            $this->persistState('filters');
        }
    }

    /**
     * @throws Exception
     */
    public function clearFilter(string $field = ''): void
    {
        $this->forgetFilter($field);

        if ($this->emitClearFiltersEvent) {
            $this->dispatch('pg:events', ['event' => 'clearFilters', 'field' => $field, 'tableName' => $this->tableName]);
        }

        $this->commitFilters();
    }

    /**
     * @throws Exception
     */
    public function clearAllFilters(): void
    {
        $this->enabledFilters = [];
        $this->filters = [];
        $this->draftFilters = [];
        $this->filterBuilder = ['match' => 'and', 'rows' => []];

        $this->dispatch('pg:clear_all_flatpickr::'.$this->tableName);
        $this->dispatch('pg:clear_all_multi_select::'.$this->tableName);

        $this->commitFilters();
    }

    /**
     * @param  array<string, array<string, mixed>>|null  $draft  DOM snapshot from pgFilterPanel.
     *                                                           When omitted, the Livewire `draftFilters`
     *                                                           property is used (tests / programmatic).
     *
     * @throws Exception
     */
    public function applyFilters(?array $draft = null): void
    {
        if (is_array($draft)) {
            $this->draftFilters = $draft;
        }

        /** @var array<string, mixed> $draft */
        $this->filters = FilterBag::hydrate(FilterKey::decodeDraft($this->draftFilters));
        $this->stampFilterTypes();

        foreach ($this->filters as $field => $record) {
            if (! FilterBag::isRecord($record)) {
                continue;
            }

            $type = $record['type'];

            if (! in_array($type, ['date', 'datetime'], true)) {
                continue;
            }

            $formatted = data_get($record, 'value.formatted');

            if (blank($formatted)) {
                unset($this->filters[$field]);

                continue;
            }

            $record['value'] = FlatpickrPlugin::computeRange(
                $type,
                is_scalar($formatted) ? (string) $formatted : ''
            );
            $this->filters[$field] = $record;
        }

        $this->commitFilters();
    }

    public function resetFilters(): void
    {
        $this->draftFilters = FilterKey::encodeDraft($this->filters);

        $this->dispatch('pg:restore_flatpickr::'.$this->tableName);
        $this->dispatch('pg:restore_multi_select::'.$this->tableName);

        if ($this->filterPanelLoaded) {
            $this->renderFilterFieldsPartial();
        }

        $this->renderFilterPanelPartial();
    }

    public function activeFilterCount(): int
    {
        return collect($this->enabledFilters)
            ->reject(fn ($filter) => ($filter['source'] ?? null) === 'filterBuilder')
            ->map(function ($filter) {
                $field = data_get($filter, 'field');
                $field = is_string($field) ? $field : '';

                return (string) str($field)->beforeLast('_start')->beforeLast('_end');
            })
            ->filter(fn ($field) => $field !== '')
            ->unique()
            ->count();
    }

    /**
     * Derive `$enabledFilters` from `$filters` + Filter Builder rows.
     * Remembers custom labels from the previous pill list (handlers, plugins).
     */
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
        foreach ($this->declaredFilters() as $filter) {
            $field = data_get($filter, 'field');
            $field = is_string($field) ? $field : '';
            $column = data_get($filter, 'column');
            $column = is_string($column) ? $column : '';

            $record = $this->filterRecord($field) ?? $this->filterRecord($column);

            if ($record === null || ! FilterBag::isActive($record)) {
                continue;
            }

            $pillField = $column !== '' ? $column : $field;
            $recordLabel = $record['label'] ?? null;
            $title = (is_string($recordLabel) && $recordLabel !== '')
                ? $recordLabel
                : ($labels[$pillField] ?? $labels[$field] ?? $labels[$column] ?? $pillField);
            $disabled = FilterBag::isValuelessOperator($record['op'] ?? null);

            $pill = [
                'field' => $pillField,
                'label' => $title,
            ];

            if ($disabled) {
                $pill['disabled'] = true;
            }

            $exists = collect($this->enabledFilters)->contains(
                fn ($enabled) => ($enabled['field'] ?? '') === $pillField
            );

            if (! $exists) {
                $this->enabledFilters[] = $pill;
            }
        }
    }

    /**
     * Stamp a pill label onto the field record. Presence is derived on the next sync.
     */
    public function addEnabledFilters(string $field, ?string $label, bool $disabled = false): void
    {
        if (! filled($label) || ! $this->isDeclaredFilterField($field)) {
            return;
        }

        $keys = [$field, FilterKey::encode($field), FilterKey::decode($field), ...$this->declaredAliasesFor($field)];

        foreach (array_unique($keys) as $alias) {
            if (! isset($this->filters[$alias]) || ! is_array($this->filters[$alias])) {
                continue;
            }

            $this->filters[$alias]['label'] = $label;

            return;
        }
    }

    /** @return list<string> */
    protected function declaredAliasesFor(string $field): array
    {
        $aliases = [];

        foreach ($this->declaredFilters() as $filter) {
            $declaredField = data_get($filter, 'field');
            $declaredColumn = data_get($filter, 'column');
            $declaredField = is_string($declaredField) ? $declaredField : '';
            $declaredColumn = is_string($declaredColumn) ? $declaredColumn : '';

            if ($field === $declaredField || $field === $declaredColumn) {
                foreach ([$declaredField, $declaredColumn] as $alias) {
                    if ($alias !== '' && $alias !== $field) {
                        $aliases[] = $alias;
                    }
                }
            }
        }

        return array_values(array_unique($aliases));
    }

    protected function isDeclaredFilterField(string $field): bool
    {
        $declared = collect($this->declaredFilters())
            ->flatMap(function ($filter) {
                $fields = [
                    data_get($filter, 'field'),
                    data_get($filter, 'column'),
                ];

                /** @var string|null $filterField */
                $filterField = data_get($filter, 'field');

                if (is_string($filterField) && data_get($filter, 'key') === 'number') {
                    $fields[] = $filterField.'_start';
                    $fields[] = $filterField.'_end';
                }

                return $fields;
            })
            ->filter(fn ($value) => is_string($value) && $value !== '');

        return $declared->contains($field);
    }

    protected function isValuelessTextOperator(mixed $operator): bool
    {
        return FilterBag::isValuelessOperator($operator);
    }

    /**
     * @return array<string, mixed>|null
     */
    protected function filterRecord(string $field): ?array
    {
        if ($field === '') {
            return null;
        }

        foreach (array_unique([$field, FilterKey::encode($field), FilterKey::decode($field)]) as $key) {
            if (! isset($this->filters[$key]) || ! is_array($this->filters[$key])) {
                continue;
            }

            /** @var array<string, mixed> $record */
            $record = $this->filters[$key];

            if (FilterBag::isRecord($record)) {
                return $record;
            }
        }

        return null;
    }

    /**
     * @return array<string, string>
     */
    protected function filterPillLabels(): array
    {
        $labels = [];

        foreach ($this->columns as $column) {
            $titleValue = data_get($column, 'title');
            $title = is_scalar($titleValue) ? (string) $titleValue : '';

            if ($title === '') {
                continue;
            }

            if (($field = data_get($column, 'field')) && is_string($field)) {
                $labels[$field] = $title;
            }

            if (($dataField = data_get($column, 'dataField')) && is_string($dataField)) {
                $labels[$dataField] = $title;
            }
        }

        foreach ($this->filters as $field => $record) {
            if (! is_string($field) || ! is_array($record)) {
                continue;
            }

            $label = $record['label'] ?? null;

            if (is_string($label) && $label !== '') {
                $labels[$field] = $label;
            }
        }

        foreach ($this->enabledFilters as $pill) {
            if (($pill['source'] ?? null) === 'filterBuilder') {
                continue;
            }

            $field = $pill['field'] ?? null;
            $label = $pill['label'] ?? null;

            if (is_string($field) && $field !== '' && is_string($label) && $label !== '') {
                $labels[$field] = $label;
            }
        }

        return $labels;
    }

    protected function forgetFilter(string $field): void
    {
        $lookup = (string) str($field)->beforeLast('_start')->beforeLast('_end');

        foreach ($this->declaredFilters() as $filter) {
            $declaredField = data_get($filter, 'field');
            $declaredColumn = data_get($filter, 'column');
            $declaredField = is_string($declaredField) ? $declaredField : '';
            $declaredColumn = is_string($declaredColumn) ? $declaredColumn : '';

            $matches = in_array($field, [$declaredField, $declaredColumn], true)
                || in_array($lookup, [$declaredField, $declaredColumn], true);

            if (! $matches) {
                continue;
            }

            $keys = [];

            foreach ([$declaredField, $declaredColumn, $field, $lookup] as $alias) {
                if ($alias === '') {
                    continue;
                }

                $keys[] = $alias;
                $keys[] = FilterKey::encode($alias);
                $keys[] = FilterKey::decode($alias);
            }

            foreach (array_unique($keys) as $key) {
                $record = $this->filterRecord($key);

                if ($record === null) {
                    unset($this->filters[$key]);

                    continue;
                }

                $type = $record['type'] ?? '';

                if ($type === 'multi_select') {
                    $this->dispatch('pg:clear_multi_select::'.$this->tableName.':'.$key);
                }

                if (in_array($type, ['date', 'datetime'], true)) {
                    $this->dispatch('pg:clear_flatpickr::'.$this->tableName.':'.$key);
                }

                unset($this->filters[$key]);
            }
        }
    }

    protected function canonicalizeFilters(bool $prune = true): void
    {
        $this->filters = FilterKey::decodeDraft($this->filters);
        $this->filters = FilterBag::hydrate($this->filters);
        $this->remapToColumnKeys();
        $this->stampFilterTypes();
        $this->normalizeValuelessTextFilters();

        if ($prune) {
            $this->filters = $this->pruneBlankFilters($this->filters);
        }
    }

    /**
     * Key the live bag by filter column (`name`), not the SQL dataField
     * (`dishes.name`), so wire:model never needs __pgdot__ on the hot path.
     */
    protected function remapToColumnKeys(): void
    {
        foreach ($this->declaredFilters() as $filter) {
            $column = data_get($filter, 'column');
            $field = data_get($filter, 'field');
            $column = is_string($column) && $column !== '' ? $column : '';
            $field = is_string($field) && $field !== '' ? $field : $column;
            $column = FilterBag::bagKey($column, $field);

            if ($column === '') {
                continue;
            }

            $fromKeys = array_unique(array_filter([
                $field !== $column ? $field : null,
                str_contains((string) $field, '.') ? FilterKey::encode((string) $field) : null,
            ]));

            foreach ($fromKeys as $from) {
                if (! isset($this->filters[$from])) {
                    continue;
                }

                if (! isset($this->filters[$column])) {
                    $this->filters[$column] = $this->filters[$from];
                }

                unset($this->filters[$from]);
            }
        }
    }

    protected function stampFilterTypes(): void
    {
        foreach ($this->declaredFilters() as $filter) {
            $type = data_get($filter, 'key');
            $type = is_string($type) ? FilterBag::normalizeType($type) : '';
            $field = data_get($filter, 'field');
            $field = is_string($field) ? $field : '';
            $column = data_get($filter, 'column');
            $column = is_string($column) ? $column : '';

            if ($type === '') {
                continue;
            }

            foreach (array_filter([$field, $column]) as $key) {
                if (! isset($this->filters[$key]) || ! is_array($this->filters[$key])) {
                    continue;
                }

                if (! isset($this->filters[$key]['type'])) {
                    $this->filters[$key]['type'] = $type;
                }
            }
        }
    }

    protected function normalizeValuelessTextFilters(): void
    {
        foreach ($this->filters as $field => $record) {
            if (! is_string($field) || ! is_array($record)) {
                continue;
            }

            /** @var array<string, mixed> $record */
            $op = $record['op'] ?? null;

            if (is_array($op)) {
                $op = collect($op)->values()->first();
                $record['op'] = $op;
            }

            if (! FilterBag::isValuelessOperator($op)) {
                $this->filters[$field] = $record;

                continue;
            }

            $record['type'] = $record['type'] ?? 'input_text';
            $record['value'] = null;
            $this->filters[$field] = $record;
        }
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    protected function pruneBlankFilters(array $filters): array
    {
        foreach ($filters as $field => $record) {
            if ($field === 'dynamic' && is_array($record) && ! FilterBag::isRecord($record)) {
                continue;
            }

            if (! FilterBag::isRecord($record)) {
                unset($filters[$field]);

                continue;
            }

            if ($record['type'] === 'number' && is_array($record['value'] ?? null)) {
                $range = $record['value'];

                if (blank($range['start'] ?? null)) {
                    unset($range['start']);
                }

                if (blank($range['end'] ?? null)) {
                    unset($range['end']);
                }

                $record['value'] = $range;
                $filters[$field] = $record;
            }

            if (! FilterBag::shouldKeep($record)) {
                unset($filters[$field]);
            }
        }

        return $filters;
    }
}
