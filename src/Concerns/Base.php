<?php

namespace PowerComponents\LivewirePowerGrid\Concerns;

use Livewire\Attributes\Computed;
use PowerComponents\LivewirePowerGrid\{Facades\PowerGrid, PowerGridFields};

trait Base
{
    public string $primaryKey = 'id';

    public ?string $primaryKeyAlias = null;

    public bool $ignoreTablePrefix = true;

    public string $tableName = '';

    public array $setUp = [];

    public bool $showErrorBag = false;

    public bool $rowIndex = true;

    public bool $deferLoading = false;

    public bool $readyToLoad = false;

    public string $loadingComponent = '';

    public array $columns = [];

    public array $headers = [];

    public string $search = '';

    public string $currentTable = '';

    public int $total = 0;

    public int $totalCurrentPage = 0;

    public bool $supportModel = true;

    public bool $paginateRaw = false;

    public bool $measurePerformance = false;

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

    public function relationSearch(): array
    {
        return [];
    }

    public function searchMorphs(): array
    {
        return [];
    }

    public function header(): array
    {
        return [];
    }

    public function setUp(): array
    {
        return [];
    }

    public function columns(): array
    {
        return [];
    }

    public function filters(): array
    {
        return [];
    }

    public function summarizeFormat(): array
    {
        return [];
    }

    public function rowTemplates(): array
    {
        return [];
    }
}
