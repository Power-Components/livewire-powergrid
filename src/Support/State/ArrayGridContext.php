<?php

namespace PowerComponents\LivewirePowerGrid\Support\State;

use Closure;
use PowerComponents\LivewirePowerGrid\{Button, PowerGridFields};
use PowerComponents\LivewirePowerGrid\Components\Filters\FilterBase;
use PowerComponents\LivewirePowerGrid\Components\Rules\BaseRule;
use PowerComponents\LivewirePowerGrid\Concerns\State\{ResolvesBeforeSearch, ResolvesGridSorting};
use PowerComponents\LivewirePowerGrid\Contracts\PowerGridContext;

final class ArrayGridContext implements PowerGridContext
{
    use ResolvesBeforeSearch;
    use ResolvesGridSorting;

    private string $currentTable = '';

    /** @var list<int|string> */
    private array $filteredKeys = [];

    /** @var array<string, mixed> */
    private array $summaryValues = [];

    /**
     * @param  Closure(mixed...): mixed  $datasourceResolver
     * @param  array<int, mixed>  $columns
     * @param  array<int, FilterBase>  $filters
     * @param  (Closure(mixed): mixed)|null  $transformQueryResolver
     * @param  array<string, list<string>|string>  $relationSearch
     * @param  array<string, string>  $searchMorphs
     * @param  (Closure(object): array<int, Button>)|null  $actionsResolver
     * @param  (Closure(object): array<int, BaseRule>)|null  $actionRulesResolver
     */
    public function __construct(
        private readonly PowerGridState $state,
        private readonly Closure $datasourceResolver,
        private readonly PowerGridFields $fields,
        private readonly array $columns = [],
        private readonly array $filters = [],
        private readonly ?Closure $transformQueryResolver = null,
        private readonly array $relationSearch = [],
        private readonly array $searchMorphs = [],
        private readonly ?Closure $actionsResolver = null,
        private readonly ?Closure $actionRulesResolver = null,
    ) {}

    /** @return array<int, Button> */
    public function actions(object $row): array
    {
        return $this->actionsResolver !== null ? (array) ($this->actionsResolver)($row) : [];
    }

    /** @return array<int, BaseRule> */
    public function actionRules(object $row): array
    {
        return $this->actionRulesResolver !== null ? (array) ($this->actionRulesResolver)($row) : [];
    }

    public function state(): PowerGridState
    {
        return $this->state;
    }

    public function datasource(mixed ...$args): mixed
    {
        return ($this->datasourceResolver)(...$args);
    }

    /** @return array<int, mixed> */
    public function declaredColumns(): array
    {
        return $this->columns;
    }

    /** @return array<int, FilterBase> */
    public function declaredFilters(): array
    {
        return $this->filters;
    }

    /** @return array<string, list<string>|string> */
    public function relationSearch(): array
    {
        return $this->relationSearch;
    }

    /** @return array<string, string> */
    public function searchMorphs(): array
    {
        return $this->searchMorphs;
    }

    public function transformQuery(mixed $query): mixed
    {
        return $this->transformQueryResolver !== null
            ? ($this->transformQueryResolver)($query)
            : $query;
    }

    public function beforeFilterBuilderApply(mixed $query, array $conditions): mixed
    {
        return $query;
    }

    public function hasSummarizeInColumns(): bool
    {
        foreach ($this->columns as $column) {
            foreach (['sum', 'count', 'avg', 'min', 'max'] as $operation) {
                if (data_get($column, "properties.summarize.{$operation}")) {
                    return true;
                }
            }
        }

        return false;
    }

    public function summariesCacheTag(): string
    {
        return 'pg-headless-'.$this->state->tableName;
    }

    public function summariesCacheKey(): string
    {
        return md5(json_encode([
            'search' => $this->state->search,
            'filters' => $this->state->filters,
            'filterBuilder' => $this->state->filterBuilder,
        ]) ?: '');
    }

    public function fields(): PowerGridFields
    {
        return $this->fields;
    }

    public function shouldCollectActions(): bool
    {
        return false;
    }

    /** @return array<mixed> */
    public function prepareActionRulesForRows(mixed $row, ?object $loop = null): array
    {
        return [];
    }

    /** @return list<array<string, mixed>> */
    public function resolveActionRules(mixed $row): array
    {
        return [];
    }

    public function getCurrentTable(): string
    {
        return $this->currentTable;
    }

    public function setCurrentTable(string $table): void
    {
        $this->currentTable = $table;
    }

    /** @param  list<int|string>  $keys */
    public function setFilteredKeys(array $keys): void
    {
        $this->filteredKeys = $keys;
    }

    /** @return list<int|string> */
    public function getFilteredKeys(): array
    {
        return $this->filteredKeys;
    }

    /** @param  array<string, mixed>  $values */
    public function setSummaryValues(array $values): void
    {
        $this->summaryValues = $values;
    }

    /** @return array<string, mixed> */
    public function getSummaryValues(): array
    {
        return $this->summaryValues;
    }

    public function resetToFirstPage(string $pageName = 'page'): void {}
}
