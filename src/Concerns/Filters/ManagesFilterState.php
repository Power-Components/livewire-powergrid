<?php

namespace PowerComponents\LivewirePowerGrid\Concerns\Filters;

use Exception;
use PowerComponents\LivewirePowerGrid\Plugins\Flatpickr\FlatpickrPlugin;
use PowerComponents\LivewirePowerGrid\Support\FilterKey;
use PowerComponents\Turbine\Components\Filters\FilterManager;

trait ManagesFilterState
{
    /** @param array<string, array<string, mixed>> $target */
    protected function setInFilters(array &$target, string $key, mixed $value): void
    {
        /** @phpstan-ignore parameterByRef.type */
        data_set($target, $key, $value);
    }

    protected function applyDefaultFilters(): void
    {
        $filterManager = new FilterManager();
        $applied = $filterManager->applyDefaults(
            declaredFilters: $this->declaredFilters(),
            columns: $this->columns,
            /** @phpstan-ignore assign.propertyType */
            filters: $this->filters,
            enabledFilters: $this->enabledFilters
        );

        if ($applied) {
            $this->persistState('filters');
        }
    }

    /**
     * @throws Exception
     */
    public function clearFilter(string $field = ''): void
    {
        collect($this->declaredFilters())
            ->each(function ($filter) use ($field) {
                $extraFieldsToClear = [];

                if (isset($this->filters['datetime']) || isset($this->filters['date'])) {
                    $this->dispatch('pg:clear_flatpickr::'.$this->tableName.':'.$field);
                }

                if (! empty($this->filters['number'])) {
                    $numberField = str($field)->beforeLast('_start')->beforeLast('_end')->append('')->toString();

                    if (isset($this->filters['number'][$numberField])) {
                        $field = $numberField;
                        $extraFieldsToClear = [$numberField.'_start', $numberField.'_end'];
                    }
                }

                if (data_get($this->filters, "multi_select.$field")) {
                    $this->dispatch('pg:clear_multi_select::'.$this->tableName.':'.$field);
                }

                if (isset($this->filters['datetime'][$field]) || isset($this->filters['date'][$field])) {
                    $this->dispatch('pg:clear_flatpickr::'.$this->tableName.':'.$field);
                }

                $declaredField = data_get($filter, 'field');
                $declaredColumn = data_get($filter, 'column');
                $declaredField = is_string($declaredField) ? $declaredField : '';
                $declaredColumn = is_string($declaredColumn) ? $declaredColumn : '';

                $matches = $field === $declaredField || $field === $declaredColumn;

                if (! $matches) {
                    return;
                }

                $aliases = array_values(array_unique(array_filter(
                    [$field, $declaredField, $declaredColumn, ...$extraFieldsToClear],
                    fn ($alias) => is_string($alias) && $alias !== '',
                )));

                /** @var string $key */
                $key = data_get($filter, 'key');

                foreach ($aliases as $alias) {
                    foreach (array_filter([$key, 'input_text_options']) as $bag) {
                        if (! isset($this->filters[$bag]) || ! is_array($this->filters[$bag])) {
                            continue;
                        }

                        if (str_contains($alias, '.')) {
                            $parts = explode('.', $alias, 2);
                            $this->removeNestedArrayKey($this->filters[$bag], $parts[0], $parts[1]);
                        }

                        unset($this->filters[$bag][$alias]);

                        if (empty($this->filters[$bag])) {
                            unset($this->filters[$bag]);
                        }
                    }
                }

                $this->enabledFilters = array_values(array_filter(
                    $this->enabledFilters,
                    fn ($enabled) => ! in_array($enabled['field'] ?? '', $aliases, true),
                ));
            });

        if ($this->emitClearFiltersEvent) {
            $this->dispatch('pg:events', ['event' => 'clearFilters', 'field' => $field, 'tableName' => $this->tableName]);
        }

        $this->draftFilters = FilterKey::encodeDraft($this->filters);

        $this->persistState('filters');

        if ($this->filterPanelLoaded) {
            $this->renderFilterFieldsPartial();
        }

        $this->renderFilterPanelPartial();
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

        $this->resetPage();

        $this->persistState('filters');

        $this->dispatch('pg:clear_all_flatpickr::'.$this->tableName);
        $this->dispatch('pg:clear_all_multi_select::'.$this->tableName);

        if ($this->filterPanelLoaded) {
            $this->renderFilterFieldsPartial();
        }

        $this->renderFilterPanelPartial();
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
        $draft = FilterKey::decodeDraft($this->draftFilters);

        foreach (['date', 'datetime'] as $dateKey) {
            /** @var array<string, mixed> $entries */
            $entries = (array) data_get($draft, $dateKey, []);

            foreach ($entries as $field => $value) {
                $formatted = data_get($value, 'formatted');

                if (blank($formatted)) {
                    unset($entries[$field]);

                    continue;
                }

                $entries[$field] = FlatpickrPlugin::computeRange($dateKey, is_scalar($formatted) ? (string) $formatted : '');
            }

            $draft[$dateKey] = $entries;

            if (empty($draft[$dateKey])) {
                unset($draft[$dateKey]);
            }
        }

        /** @var array<string, mixed> $operators */
        $operators = (array) data_get($draft, 'input_text_options', []);

        foreach ($operators as $field => $operator) {
            if (in_array($operator, ['is_empty', 'is_not_empty', 'is_null', 'is_not_null', 'is_blank', 'is_not_blank'], true)) {
                data_set($draft, 'input_text.'.$field, null);
            }
        }

        /** @var array<string, mixed> $draft */
        $filters = $this->pruneBlankFilters($draft);

        $this->filters = $filters;
        $this->draftFilters = FilterKey::encodeDraft($filters);

        $this->rebuildEnabledFilters();
        $this->syncFilterBuilderPills();

        $this->resetPage();
        $this->persistState('filters');
        $this->renderFilterPanelPartial();
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

    protected function rebuildEnabledFilters(): void
    {
        $this->enabledFilters = [];

        $titles = [];

        foreach ($this->columns as $column) {
            $titleValue = data_get($column, 'title');
            $title = is_scalar($titleValue) ? (string) $titleValue : '';

            if (($field = data_get($column, 'field')) && is_string($field)) {
                $titles[$field] = $title;
            }

            if (($dataField = data_get($column, 'dataField')) && is_string($dataField)) {
                $titles[$dataField] = $title;
            }
        }

        foreach ($this->declaredFilters() as $filter) {
            $key = data_get($filter, 'key');
            $key = is_string($key) ? $key : '';
            $field = data_get($filter, 'field');
            $field = is_string($field) ? $field : '';
            $column = data_get($filter, 'column');
            $column = is_string($column) ? $column : '';

            $title = $titles[$column] ?? $titles[$field] ?? $field;

            $typeBag = (array) ($this->filters[$key] ?? []);
            $pillField = $column !== '' ? $column : $field;

            if ($key === 'number') {
                $range = $typeBag[$field] ?? $typeBag[$column] ?? null;

                if (filled(data_get($range, 'start')) || filled(data_get($range, 'end'))) {
                    $this->addEnabledFilters($pillField, $title);
                }

                continue;
            }

            if (filled($typeBag[$field] ?? null) || filled($typeBag[$column] ?? null)) {
                $this->addEnabledFilters($pillField, $title);
            }
        }

        /** @var array<string, mixed> $operators */
        $operators = (array) data_get($this->filters, 'input_text_options', []);

        foreach ($operators as $field => $operator) {
            if (in_array($operator, ['is_empty', 'is_not_empty', 'is_null', 'is_not_null', 'is_blank', 'is_not_blank'], true)) {
                $this->addEnabledFilters(strval($field), $titles[$field] ?? strval($field));
            }
        }
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array<string, array<string, mixed>>
     */
    protected function pruneBlankFilters(array $filters): array
    {
        foreach ($filters as $key => $values) {
            if (! is_array($values)) {
                if (blank($values)) {
                    unset($filters[$key]);
                }

                continue;
            }

            foreach ($values as $field => $value) {
                if (! is_array($value) && blank($value)) {
                    unset($values[$field]);
                }

                if (is_array($value) && $key === 'multi_select' && blank(array_filter($value, fn ($v) => filled($v)))) {
                    unset($values[$field]);
                }
            }

            if (empty($values)) {
                unset($filters[$key]);

                continue;
            }

            $filters[$key] = $values;
        }

        /** @var array<string, array<string, mixed>> $filters */
        return $filters;
    }

    public function addEnabledFilters(string $field, ?string $label): void
    {
        if (! $this->isDeclaredFilterField($field)) {
            return;
        }

        if (! collect($this->enabledFilters)
            ->where('field', $field)
            ->count()) {
            $this->enabledFilters[] = [
                'field' => $field,
                'label' => $label ?? '',
            ];
        }
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

    /** @param  array<string, mixed>  $array */
    protected function removeNestedArrayKey(array &$array, string $parent, string $child): void
    {
        if (isset($array[$parent]) && is_array($array[$parent])) {
            /** @var array<string, mixed> $nested */
            $nested = &$array[$parent];

            if (isset($nested[$child])) {
                unset($nested[$child]);
            }

            if (empty($array[$parent])) {
                unset($array[$parent]);
            }
        }
    }
}
