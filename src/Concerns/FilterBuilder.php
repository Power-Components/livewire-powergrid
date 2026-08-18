<?php

namespace PowerComponents\LivewirePowerGrid\Concerns;

use Exception;
use PowerComponents\Turbine\Plugins\FilterBuilder\FilterBuilderValidator;

trait FilterBuilder
{
    /** @var array<string, mixed> */
    public array $filterBuilder = ['match' => 'and', 'rows' => []];

    /**
     * @param  array<string, mixed>  $payload
     *
     * @throws Exception
     */
    public function applyFilterBuilder(array $payload = []): void
    {
        /** @var int|string $maxConditions */
        $maxConditions = data_get($this->setUp, 'filterBuilder.maxConditions', 30);

        $conditions = FilterBuilderValidator::validate(
            $payload,
            FilterBuilderValidator::columnsMeta($this),
            intval($maxConditions),
        );

        $this->validateFilterBuilder($conditions);

        $this->filterBuilder = $conditions;

        $this->resetPage();
        $this->syncFilterBuilderPills();
        $this->persistState('filters');
        $this->renderOutsideFiltersPartial();
    }

    /**
     * @throws Exception
     */
    public function resetFilterBuilder(): void
    {
        $this->filterBuilder = ['match' => 'and', 'rows' => []];

        $this->resetPage();
        $this->syncFilterBuilderPills();
        $this->persistState('filters');
        $this->renderOutsideFiltersPartial();
    }

    /**
     * @throws Exception
     */
    public function clearFilterBuilderRow(int $index): void
    {
        $rows = array_values((array) data_get($this->filterBuilder, 'rows', []));

        unset($rows[$index]);

        $this->filterBuilder['rows'] = array_values($rows);

        $this->resetPage();
        $this->syncFilterBuilderPills();
        $this->persistState('filters');
        $this->renderOutsideFiltersPartial();
    }

    public function filterBuilderHidesDefaultFilters(): bool
    {
        return (bool) data_get($this->setUp, 'filterBuilder.hideDefaultFilters', false);
    }

    public function filterBuilderPersists(): bool
    {
        return (bool) data_get($this->setUp, 'filterBuilder.persist', false);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function filterBuilderMeta(): array
    {
        return collect(FilterBuilderValidator::columnsMeta($this))
            ->map(fn ($column) => [
                'field' => $column['field'],
                'title' => $column['title'],
                'type' => $column['type'],
                'operators' => $column['operators'],
                'options' => $column['options'],
            ])
            ->values()
            ->all();
    }

    private function syncFilterBuilderPills(): void
    {
        $meta = FilterBuilderValidator::columnsMeta($this);

        $this->enabledFilters = array_values(array_filter(
            $this->enabledFilters,
            fn ($filter) => data_get($filter, 'source') !== 'filterBuilder'
        ));

        foreach (array_values((array) data_get($this->filterBuilder, 'rows', [])) as $index => $row) {
            /** @var array<string, mixed> $rowData */
            $rowData = (array) $row;

            /** @var string $column */
            $column = data_get($rowData, 'column', '');
            /** @var string $title */
            $title = data_get($meta, "$column.title", $column);

            $this->enabledFilters[] = [
                'field' => $column,
                'label' => $this->filterBuilderPillLabel($rowData, $title),
                'source' => 'filterBuilder',
                'index' => $index,
            ];
        }
    }

    /**
     * @param  array<string, mixed>  $row
     */
    private function filterBuilderPillLabel(array $row, string $title): string
    {
        /** @var string $operatorKey */
        $operatorKey = data_get($row, 'operator', '');

        $operator = trans('livewire-powergrid::datatable.input_text_options.'.$operatorKey);

        if (! is_string($operator) || $operator === 'livewire-powergrid::datatable.input_text_options.'.$operatorKey) {
            $operator = str_replace('_', ' ', $operatorKey);
        }

        if (in_array($operatorKey, FilterBuilderValidator::VALUELESS_OPERATORS, true)) {
            return trim("{$title} {$operator}");
        }

        $value = is_scalar(data_get($row, 'value')) ? strval(data_get($row, 'value')) : '';

        if (in_array($operatorKey, FilterBuilderValidator::RANGE_OPERATORS, true)) {
            $value2 = is_scalar(data_get($row, 'value2')) ? strval(data_get($row, 'value2')) : '';

            return "{$title}: {$value} - {$value2}";
        }

        return "{$title}: {$value}";
    }
}
