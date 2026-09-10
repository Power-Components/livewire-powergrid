<?php

namespace PowerComponents\LivewirePowerGrid\Concerns\Filters;

use Illuminate\Support\Collection;

trait ManagesFilterPanel
{
    public function toggleFilters(): void
    {
        $this->showFilters = ! $this->showFilters;

        if ($this->showFilters) {
            $this->mountFilterPanel();
        }

        $this->renderFilterPanelPartial();
    }

    public function updatedShowFilters(): void
    {
        if ($this->showFilters) {
            $this->mountFilterPanel();
        }

        $this->renderFilterPanelPartial();
    }

    public function loadFilterPanel(): void
    {
        $this->mountFilterPanel();
    }

    protected function mountFilterPanel(): void
    {
        if ($this->filterPanelLoaded) {
            return;
        }

        $this->filterPanelLoaded = true;

        $this->renderFilterFieldsPartial();
    }

    protected function renderFilterFieldsPartial(): void
    {
        if (! function_exists('partials') || ! $this->usesFilterPanel()) {
            return;
        }

        $this->resolveFiltersForRender();

        /** @var view-string $view */
        $view = 'livewire-powergrid::components.themes.tailwind.filters.fields';

        partials($this)
            ->partial("pg-filter-fields-{$this->tableName}", $view, [
                '__partial' => $this,
                'tableName' => $this->tableName,
            ]);
    }

    public function filterPosition(): string
    {
        $position = config('livewire-powergrid.filter');

        return is_string($position) ? $position : '';
    }

    /** True when filters render inline, inside the table header row. */
    public function usesFilterInline(): bool
    {
        return $this->filterPosition() === 'inline';
    }

    /** True when filters live in their own panel instead of inside the table. */
    public function usesFilterPanel(): bool
    {
        return in_array($this->filterPosition(), ['dropdown', 'flyout'], true);
    }

    public function usesFilterDropdown(): bool
    {
        return $this->filterPosition() === 'dropdown';
    }

    public function usesFilterFlyout(): bool
    {
        return $this->filterPosition() === 'flyout';
    }

    public function filterPanelView(): string
    {
        return $this->filterPosition() === 'flyout' ? 'filter.flyout' : 'filter.dropdown';
    }

    public function filterPanelColumns(): int
    {
        $count = count($this->declaredFilters());

        return match (true) {
            $count > 6 => 3,
            $count > 4 => 2,
            default => 1,
        };
    }

    /**
     * Filter-bearing columns for dropdown/flyout panels.
     * Uses Filter::order() when set, otherwise the filters() array index.
     *
     * @param  iterable<array-key, mixed>|null  $columns
     * @return Collection<int, mixed>
     */
    public function sortedFilterPanelColumns(?iterable $columns = null): Collection
    {
        $source = collect($columns ?? [])
            ->filter(fn ($column) => filled(data_get($column, 'filters')));

        if ($source->isEmpty()) {
            $source = collect($this->columns)
                ->filter(fn ($column) => filled(data_get($column, 'filters')));
        }

        $declarationOrder = collect($this->declaredFilters())
            ->values()
            ->mapWithKeys(function ($filter, int $index): array {
                $field = data_get($filter, 'field');
                $key = is_string($field) || is_numeric($field) ? (string) $field : (string) $index;

                return [$key => $index];
            });

        return $source
            ->sortBy(function ($column) use ($declarationOrder): string {
                $field = data_get($column, 'filters.field');
                $fieldKey = is_string($field) || is_numeric($field) ? (string) $field : '';
                $declared = $declarationOrder->get($fieldKey, PHP_INT_MAX);
                $explicit = data_get($column, 'filters.order');
                $order = is_numeric($explicit) ? (int) $explicit : $declared;

                return sprintf('%010d-%010d', $order, $declared);
            })
            ->values();
    }

    /**
     * @return array{position: string, close_on_escape: bool, close_on_click_outside: bool}
     */
    public function filterFlyoutOptions(): array
    {
        $position = config('livewire-powergrid.filter_flyout.position', 'right');

        return [
            'position' => in_array($position, ['left', 'right'], true) ? $position : 'right',
            'close_on_escape' => boolval(config('livewire-powergrid.filter_flyout.close_on_escape', true)),
            'close_on_click_outside' => boolval(config('livewire-powergrid.filter_flyout.close_on_click_outside', true)),
        ];
    }

    public function renderFilterPanelPartial(bool $includeGrid = true): void
    {
        if (! function_exists('partials')) {
            return;
        }

        $this->resolveFiltersForRender();

        partials($this)
            ->partial("pg-enabled-filters-{$this->tableName}", theme_view('header.enabled-filters'), [
                'enabledFilters' => $this->enabledFilters,
            ]);

        if ($includeGrid) {
            $this->renderGridPartials();
        }

        if (! $this->usesFilterPanel()) {
            return;
        }

        if ($this->usesFilterFlyout()) {
            partials($this)
                ->partial("pg-filter-trigger-{$this->tableName}", theme_view('header.filters'));
        }
    }
}
