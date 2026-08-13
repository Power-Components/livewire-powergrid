<?php

namespace PowerComponents\LivewirePowerGrid\Contracts;

use Closure;
use PowerComponents\LivewirePowerGrid\Components\Filters\FilterBase;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Support\State\PowerGridState;

/**
 * @method mixed datasource(mixed ...$args)
 * @method list<\PowerComponents\LivewirePowerGrid\Button> actions(object $row)
 * @method array<int, mixed> actionRules(object $row)
 */
interface PowerGridContext
{
    public function state(): PowerGridState;

    /** @return array<int, mixed> */
    public function declaredColumns(): array;

    /** @return array<int, FilterBase> */
    public function declaredFilters(): array;

    /** @return array<string, list<string>|string> */
    public function relationSearch(): array;

    /** @return array<string, string> */
    public function searchMorphs(): array;

    public function transformQuery(mixed $query): mixed;

    public function resolveSortField(string $sortField): string;

    public function isValidSortField(string $sortField): bool;

    public function getSortCallback(string $field): ?Closure;

    public function applyBeforeSearch(string $field, ?string $search): ?string;

    /** @param  array<string, mixed>  $conditions */
    public function beforeFilterBuilderApply(mixed $query, array $conditions): mixed;

    public function hasSummarizeInColumns(): bool;

    public function summariesCacheTag(): string;

    public function summariesCacheKey(): string;

    public function fields(): PowerGridFields;

    public function shouldCollectActions(): bool;

    /** @return array<mixed> */
    public function prepareActionRulesForRows(mixed $row, ?object $loop = null): array;

    /** @return list<array<string, mixed>> */
    public function resolveActionRules(mixed $row): array;

    public function getCurrentTable(): string;

    public function setCurrentTable(string $table): void;

    /** @param  list<int|string>  $keys */
    public function setFilteredKeys(array $keys): void;

    /** @param  array<string, mixed>  $values */
    public function setSummaryValues(array $values): void;

    public function resetToFirstPage(string $pageName = 'page'): void;
}
