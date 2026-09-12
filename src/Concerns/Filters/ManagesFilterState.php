<?php

namespace PowerComponents\LivewirePowerGrid\Concerns\Filters;

use Exception;
use PowerComponents\LivewirePowerGrid\Plugins\PluginBase;
use PowerComponents\LivewirePowerGrid\Support\FilterKey;
use PowerComponents\Turbine\Components\Filters\FilterManager;
use PowerComponents\Turbine\Support\FilterBag;

/**
 * Canonical filter bag state.
 *
 * Invariant: `$filters` is keyed by the declared filter's bag key
 * (FilterBag::bagKey). Every other spelling a key can arrive in — the SQL
 * field, a `__pgdot__`-encoded field, a legacy `_start`/`_end` bound — is
 * canonicalized once, at the boundary (canonicalizeFilters), so reads
 * downstream are plain array lookups.
 */
trait ManagesFilterState
{
    use DerivesFilterPills;

    /** @var array{aliases: array<string, string>, types: array<string, string>}|null */
    protected ?array $filterSchemaCache = null;

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
        $key = $this->filterBagKey($field) ?: $field;
        $record = is_array($this->filters[$key] ?? null) ? $this->filters[$key] : [];
        $record['type'] = FilterBag::normalizeType($type);
        $record['value'] = $value;

        if ($op !== null) {
            $record['op'] = $op;
        }

        if (filled($label)) {
            $record['label'] = $label;
        }

        $this->filters[$key] = $record;
    }

    /**
     * Prune blanks, derive pills, persist, and refresh filter UI.
     *
     * @throws Exception
     */
    public function commitFilters(bool $resetPage = true): void
    {
        $this->canonicalizeFilters();
        $this->draftFilters = FilterKey::encodeDraft($this->bagWithOperators());
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
        $this->canonicalizeFilters(prune: false);

        $filterManager = new FilterManager();
        $applied = $filterManager->applyDefaults(
            declaredFilters: $this->declaredFilters(),
            /** @phpstan-ignore assign.propertyType */
            filters: $this->filters,
        );

        $this->canonicalizeFilters(prune: false);
        $this->syncFilterPills();
        $this->draftFilters = FilterKey::encodeDraft($this->bagWithOperators());

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
        $this->filterOperators = [];
        $this->filterBuilder = ['match' => 'and', 'rows' => []];

        $this->notifyFilterWidgets(fn ($plugin) => $plugin->onFiltersCleared());

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

        $this->filters = $this->draftFilters;
        $this->canonicalizeFilters(prune: false);
        $this->normalizeFilterRecordsWithPlugins();

        $this->commitFilters();
    }

    public function resetFilters(): void
    {
        $this->draftFilters = FilterKey::encodeDraft($this->bagWithOperators());

        $this->notifyFilterWidgets(fn ($plugin) => $plugin->onFiltersRestored());

        if ($this->filterPanelLoaded) {
            $this->renderFilterFieldsPartial();
        }

        $this->renderFilterPanelPartial();
    }

    /** The SQL field a declared filter targets. */
    protected function declaredFilterField(mixed $filter): string
    {
        $field = data_get($filter, 'field');

        return is_string($field) ? $field : '';
    }

    /**
     * Canonical bag key for any spelling of a declared filter field
     * (column, SQL field, encoded field, `_start`/`_end` bound).
     * Returns '' for fields no filter declares — the mass-assignment guard.
     */
    protected function filterBagKey(string $field): string
    {
        $aliases = $this->filterSchema()['aliases'];

        return $field === '' ? '' : ($aliases[$field] ?? $aliases[FilterKey::decode($field)] ?? '');
    }

    /**
     * @return array<string, mixed>|null
     */
    protected function filterRecord(string $field): ?array
    {
        $record = $this->filters[$this->filterBagKey($field) ?: $field] ?? null;

        /** @var array<string, mixed>|null $record */
        return FilterBag::isRecord($record) ? $record : null;
    }

    protected function forgetFilter(string $field): void
    {
        $key = $this->filterBagKey($field);

        if ($key === '' || ! isset($this->filters[$key])) {
            return;
        }

        $record = $this->filters[$key];
        $type = is_array($record) ? ($record['type'] ?? '') : '';

        if (is_string($type) && $type !== '') {
            $this->notifyFilterWidgets(fn ($plugin) => $plugin->onFilterCleared($key, $type));
        }

        unset($this->filters[$key]);
    }

    protected function canonicalizeFilters(bool $prune = true): void
    {
        $this->filters = $this->remapToBagKeys($this->filters);
        $this->normalizeFilterRecords($prune);
    }

    /**
     * Every spelling a declared filter can be keyed by, plus the type each bag
     * key carries. Memoized per request.
     *
     * @return array{aliases: array<string, string>, types: array<string, string>}
     */
    protected function filterSchema(): array
    {
        if ($this->filterSchemaCache !== null) {
            return $this->filterSchemaCache;
        }

        $declared = $this->declaredFilters();

        if ($declared === []) {
            return ['aliases' => [], 'types' => []];
        }

        $aliases = $bounds = $types = [];

        foreach ($declared as $filter) {
            $field = $this->declaredFilterField($filter);
            $column = data_get($filter, 'column');
            $column = is_string($column) && $column !== '' ? $column : $field;
            $bagKey = FilterBag::bagKey($column, $field !== '' ? $field : null);

            if ($bagKey === '') {
                continue;
            }

            foreach ([$bagKey, $field, $column, FilterKey::encode($field), FilterKey::encode($column)] as $alias) {
                if ($alias !== '') {
                    $aliases[$alias] ??= $bagKey;
                }
            }

            $type = data_get($filter, 'key');

            if (! is_string($type) || $type === '') {
                continue;
            }

            $types[$bagKey] ??= FilterBag::normalizeType($type);

            if ($type === 'number' && $field !== '') {
                $bounds[$field.'_start'] = $bagKey;
                $bounds[$field.'_end'] = $bagKey;
            }
        }

        // Bounds are aliases of last resort: a real filter field always wins.
        return $this->filterSchemaCache = ['aliases' => $aliases + $bounds, 'types' => $types];
    }

    /**
     * Fold every alias key onto its bag key. Keys no filter declares (e.g.
     * FilterDynamic paths) are kept, decoded, exactly as they arrived.
     *
     * @param  array<string, mixed>  $bag
     * @return array<string, mixed>
     */
    protected function remapToBagKeys(array $bag): array
    {
        $out = $aliases = [];

        foreach ($bag as $key => $record) {
            $key = (string) $key;
            $canonical = $this->filterBagKey($key) ?: FilterKey::decode($key);

            if ($canonical === $key) {
                $out[$key] = $record;

                continue;
            }

            $aliases[$canonical] ??= $record;
        }

        // A key already canonical wins over an alias folded onto it.
        return $out + $aliases;
    }

    /**
     * One pass over the bag: stamp the declared type, settle the operator
     * (remembered in `$filterOperators`, not in the value bag), blank the value
     * of an operator that takes none, and — when pruning — drop everything that
     * would not filter anything.
     */
    protected function normalizeFilterRecords(bool $prune): void
    {
        $types = $this->filterSchema()['types'];

        foreach ($this->filters as $field => $record) {
            if (! is_string($field) || ! is_array($record)) {
                continue;
            }

            if (! isset($record['type']) && isset($types[$field])) {
                $record['type'] = $types[$field];
            }

            $op = $record['op'] ?? null;
            $op = is_array($op) ? collect($op)->values()->first() : $op;

            if (is_string($op) && $op !== '') {
                $this->filterOperators[$field] = $op;
            } else {
                $op = $this->filterOperators[$field] ?? null;
            }

            if (is_string($op) && $op !== '') {
                $record['op'] = $op;
            }

            if (FilterBag::isValuelessOperator($op)) {
                $record['type'] ??= 'input_text';
                $record['value'] = null;
            }

            if (($record['type'] ?? null) === 'number' && is_array($record['value'] ?? null)) {
                $record['value'] = array_filter($record['value'], fn ($bound) => filled($bound));
            }

            $this->filters[$field] = $record;

            // `dynamic` is a free-form bag of FilterDynamic paths, not a record.
            if (! $prune || $field === 'dynamic') {
                continue;
            }

            if (! FilterBag::isRecord($record) || ! FilterBag::isActive($record)) {
                unset($this->filters[$field]);
            }
        }
    }

    /**
     * The value bag plus the remembered operators — what the filter inputs bind
     * to, so a chosen operator survives a cleared value.
     *
     * @return array<string, mixed>
     */
    protected function bagWithOperators(): array
    {
        $bag = $this->filters;

        foreach ($this->filterOperators as $field => $op) {
            if (isset($bag[$field]) && is_array($bag[$field])) {
                $bag[$field]['op'] ??= $op;

                continue;
            }

            $bag[$field] = ['op' => $op];
        }

        return $bag;
    }

    /**
     * Let the plugin that owns a filter type reshape its draft record before
     * it is applied (e.g. Flatpickr turning a formatted range into start/end).
     * A plugin returning null drops the filter.
     */
    protected function normalizeFilterRecordsWithPlugins(): void
    {
        foreach ($this->filters as $field => $record) {
            if (! is_string($field) || ! FilterBag::isRecord($record)) {
                continue;
            }

            $normalized = $record;

            $this->notifyFilterWidgets(function ($plugin) use ($field, &$normalized) {
                if (is_array($normalized)) {
                    $normalized = $plugin->normalizeFilterRecord($field, $normalized);
                }
            });

            if ($normalized === null) {
                unset($this->filters[$field]);

                continue;
            }

            $this->filters[$field] = $normalized;
        }
    }

    /**
     * @param  callable(PluginBase): void  $callback
     */
    protected function notifyFilterWidgets(callable $callback): void
    {
        $this->resolvePlugins();

        foreach ($this->getPlugins() as $plugin) {
            $callback($plugin);
        }
    }
}
