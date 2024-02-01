<?php

namespace PowerComponents\LivewirePowerGrid\Concerns;

use PowerComponents\LivewirePowerGrid\ProcessDataSource;

trait Base
{
    public string $primaryKey = 'id';

    public bool $ignoreTablePrefix = true;

    public string $tableName = 'default';

    public array $setUp = [];

    public bool $showErrorBag = false;

    public bool $rowIndex = true;

    public array $searchMorphs = [];

    public bool $deferLoading = false;

    public bool $readyToLoad = false;

    public string $loadingComponent = '';

    public array $columns = [];

    protected ?ProcessDataSource $processDataSourceInstance = null;

    public array $actions = [];

    public array $headers = [];

    public string $search = '';

    public string $currentTable = '';

    public array $relationSearch = [];

    public int $total = 0;

    public int $totalCurrentPage = 0;

    public function template(): ?string
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
}
