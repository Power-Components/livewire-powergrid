<?php

namespace PowerComponents\LivewirePowerGrid\Concerns;

use Exception;
use PowerComponents\LivewirePowerGrid\Plugins\FilterBuilder\FilterBuilderValidator;

/**
 * State + Livewire actions for the Filter Builder (Flux-only modal plugin).
 *
 * $filterBuilder holds the APPLIED conditions (untrusted, persisted). The modal
 * draft is edited client-side (Alpine) and only committed via applyFilterBuilder().
 */
trait FilterBuilder
{
    /** @var array<string, mixed> shape: {match: 'and'|'or', rows: list<array{column, operator, value, value2}>} */
    public array $filterBuilder = ['match' => 'and', 'rows' => []];

    /**
     * Commit the draft conditions from the modal. The payload is validated and
     * normalized against the filters() allowlist before it is stored.
     *
     * @param  array<string, mixed>  $payload
     *
     * @throws Exception
     */
    public function applyFilterBuilder(array $payload = []): void
    {
        /** @var int|string $maxConditions */
        $maxConditions = data_get($this->setUp, 'filterBuilder.maxConditions', 30);

        $this->filterBuilder = FilterBuilderValidator::validate(
            $payload,
            FilterBuilderValidator::columnsMeta($this),
            intval($maxConditions),
        );

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
     * Remove a single applied condition (used by its enabled-filter pill).
     *
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

    /**
     * Whether the builder is configured to hide the default inline/outside
     * filters. Read by the theme blades to gate the per-column filter row, the
     * "Filters" toggle button and the outside filter panel.
     */
    public function filterBuilderHidesDefaultFilters(): bool
    {
        return (bool) data_get($this->setUp, 'filterBuilder.hideDefaultFilters', false);
    }

    /**
     * Column metadata for the modal (allowlist + operators + options), without
     * the non-serializable FilterBase definitions.
     *
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

    /**
     * Rebuild the enabled-filter pills contributed by the builder, tagging each
     * with source=filterBuilder + its row index so the pill removes that row.
     */
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
