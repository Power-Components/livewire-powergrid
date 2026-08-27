<?php

namespace PowerComponents\LivewirePowerGrid\Concerns;

use Livewire\Attributes\Computed;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\Turbine\Components\Filters\FilterBase;
use PowerComponents\Turbine\Contracts\GridSchema;

trait Base
{
    public string $primaryKey = 'id';

    public ?string $primaryKeyAlias = null;

    public bool $ignoreTablePrefix = true;

    public string $tableName = '';

    /** @var array<string, mixed> */
    public array $setUp = [];

    public bool $showErrorBag = false;

    public bool $rowIndex = true;

    public bool $deferLoading = false;

    public bool $readyToLoad = false;

    public string $loadingComponent = '';

    /** @var list<mixed> */
    public array $columns = [];

    /** @var list<mixed> */
    public array $headers = [];

    public string $search = '';

    public string $currentTable = '';

    public int $totalCurrentPage = 0;

    public bool $supportModel = true;

    public bool $paginateRaw = false;

    public bool $isExporting = false;

    public bool $pruneHiddenColumns = true;

    /**
     * Rebuild `$setUp` / `$columns` from the server-declared methods and copy
     * only client-owned fields (column hidden flags, footer perPage).
     *
     * @param  list<mixed>  $incomingColumns
     * @param  array<string, mixed>  $incomingSetUp
     */
    protected function rebindServerOwnedState(array $incomingColumns = [], array $incomingSetUp = []): void
    {
        $incomingColumns = $incomingColumns !== [] ? $incomingColumns : $this->columns;
        $incomingSetUp = $incomingSetUp !== [] ? $incomingSetUp : $this->setUp;

        $this->setUp = [];

        foreach ($this->setUp() as $setUp) {
            $name = is_object($setUp) ? data_get($setUp, 'name') : null;

            if (is_string($name)) {
                $this->setUp[$name] = $setUp;
            }
        }

        $this->overlayFooterPerPage($incomingSetUp);

        $this->declaredColumnsCache = null;
        $this->columns = array_map(
            static fn ($column) => is_object($column) ? clone $column : $column,
            $this->declaredColumns(),
        );
        $this->overlayColumnHidden($incomingColumns);

        unset($this->visibleColumns, $this->columnViewModels, $this->hasColumnFilters);
    }

    /** @param  array<string, mixed>  $incomingSetUp */
    private function overlayFooterPerPage(array $incomingSetUp): void
    {
        if (! isset($this->setUp['footer'])) {
            return;
        }

        $perPage = data_get($incomingSetUp, 'footer.perPage');

        if (! is_numeric($perPage)) {
            return;
        }

        $perPage = (int) $perPage;
        $allowed = (array) data_get($this->setUp, 'footer.perPageValues', []);

        if ($allowed !== [] && ! in_array($perPage, $allowed, true)) {
            return;
        }

        $maxConfig = config('livewire-powergrid.max_per_page', config('turbine.max_per_page', 1000));
        $maxPerPage = is_numeric($maxConfig) ? (int) $maxConfig : 1000;

        if ($maxPerPage > 0 && $perPage > $maxPerPage) {
            $perPage = $maxPerPage;
        }

        $footer = (array) data_get($this->setUp, 'footer', []);
        $footer['perPage'] = $perPage;
        $this->setUp['footer'] = $footer;
    }

    /** @param  list<mixed>  $incomingColumns */
    private function overlayColumnHidden(array $incomingColumns): void
    {
        $hiddenByField = [];

        foreach ($incomingColumns as $column) {
            if ((bool) data_get($column, 'forceHidden')) {
                continue;
            }

            $hidden = (bool) data_get($column, 'hidden');

            foreach ($this->columnIdentityKeys($column) as $key) {
                $hiddenByField[$key] = $hidden;
            }
        }

        foreach ($this->columns as $index => $column) {
            if ((bool) data_get($column, 'forceHidden')) {
                continue;
            }

            foreach ($this->columnIdentityKeys($column) as $key) {
                if (array_key_exists($key, $hiddenByField)) {
                    data_set($column, 'hidden', $hiddenByField[$key]);
                    $this->columns[$index] = $column;

                    break;
                }
            }
        }
    }

    /** @return list<string> */
    private function columnIdentityKeys(mixed $column): array
    {
        $keys = [];

        foreach (['field', 'dataField'] as $attr) {
            $value = data_get($column, $attr);

            if (is_string($value) && $value !== '') {
                $keys[] = $value;
            }
        }

        return array_values(array_unique($keys));
    }

    protected function definition(): ?GridSchema
    {
        return null;
    }

    public function fields(): PowerGridFields
    {
        $definition = $this->definition();

        if ($definition === null) {
            return PowerGrid::fields();
        }

        $fields = PowerGrid::fields();

        foreach ($definition->fields()->fields as $name => $closure) {
            $fields->add($name, $closure);
        }

        return $fields;
    }

    #[Computed]
    public function realPrimaryKey(): string
    {
        return $this->primaryKeyAlias ?? $this->primaryKey;
    }

    /** @var list<mixed>|null */
    private ?array $declaredColumnsCache = null;

    /** @var list<FilterBase>|null */
    private ?array $declaredFiltersCache = null;

    private bool $deferFilterInstantiation = false;

    public function customThemeClass(): ?string
    {
        return null;
    }

    /**
     * @return list<mixed>
     */
    public function declaredColumns(): array
    {
        return $this->declaredColumnsCache ??= array_values($this->columns());
    }

    public function hasResolvedColumns(): bool
    {
        return ! empty($this->declaredColumns());
    }

    /**
     * Server-declared filters, memoized once per request.
     *
     * @return list<FilterBase>
     */
    public function declaredFilters(): array
    {
        if ($this->deferFilterInstantiation && $this->declaredFiltersCache === null) {
            return [];
        }

        return $this->declaredFiltersCache ??= $this->filters();
    }

    /**
     * Run $callback without calling filters() (and its dataSource queries).
     * Used when morphing tbody/thead/pagination only — dropdown/flyout
     * filter UI is not in those partials.
     */
    protected function withoutFilterInstantiation(callable $callback): mixed
    {
        $this->deferFilterInstantiation = true;

        try {
            return $callback();
        } finally {
            $this->deferFilterInstantiation = false;
        }
    }

    /** @return array<string, list<string>> */
    public function relationSearch(): array
    {
        $definition = $this->definition();

        if ($definition === null) {
            return [];
        }

        $out = [];

        foreach ($definition->relationSearch() as $relation => $columns) {
            $out[$relation] = is_array($columns) ? array_values($columns) : [$columns];
        }

        return $out;
    }

    /** @return array<string, string> */
    public function searchMorphs(): array
    {
        return $this->definition()?->searchMorphs() ?? [];
    }

    /** @return list<mixed> */
    public function header(): array
    {
        return [];
    }

    /** @return array<string, mixed> */
    public function setUp(): array
    {
        $definition = $this->definition();

        if ($definition === null) {
            return [];
        }

        $out = [];

        foreach ($definition->setUp() as $component) {
            $name = data_get($component, 'name');

            if (is_string($name)) {
                $out[$name] = $component;
            }
        }

        return $out;
    }

    /** @return list<mixed> */
    public function columns(): array
    {
        return array_values($this->definition()?->columns() ?? []);
    }

    /** @return list<FilterBase> */
    public function filters(): array
    {
        return array_values($this->definition()?->filters() ?? []);
    }

    /** @return array<string, mixed> */
    public function summarizeFormat(): array
    {
        return [];
    }

    /** @return array<string, mixed> */
    public function rowTemplates(): array
    {
        return [];
    }
}
