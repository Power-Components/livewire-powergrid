<?php

namespace PowerComponents\LivewirePowerGrid\Concerns;

use Livewire\Attributes\Computed;
use PowerComponents\LivewirePowerGrid\{Components\Filters\FilterBase, Facades\PowerGrid, PowerGridFields};

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

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields();
    }

    #[Computed]
    public function realPrimaryKey(): string
    {
        return $this->primaryKeyAlias ?? $this->primaryKey;
    }

    public function customThemeClass(): ?string
    {
        return null;
    }

    /** @return array<string, list<string>> */
    public function relationSearch(): array
    {
        return [];
    }

    /** @return array<string, string> */
    public function searchMorphs(): array
    {
        return [];
    }

    /** @return list<mixed> */
    public function header(): array
    {
        return [];
    }

    /** @return array<string, mixed> */
    public function setUp(): array
    {
        return [];
    }

    /** @return list<mixed> */
    public function columns(): array
    {
        return [];
    }

    /** @return list<FilterBase> */
    public function filters(): array
    {
        return [];
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
