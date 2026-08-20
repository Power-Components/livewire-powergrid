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
        return $this->declaredFiltersCache ??= $this->filters();
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
