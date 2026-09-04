<?php

namespace PowerComponents\LivewirePowerGrid\Concerns;

use Illuminate\Database\Eloquent\{Builder as EloquentBuilder, Model};
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Pipeline\Pipeline;
use Livewire\Attributes\Locked;
use PowerComponents\LivewirePowerGrid\Components\SetUp\{Tab, Tabs};
use PowerComponents\Turbine\DataSource\Processors\Database\Pipelines\Filters;

trait HasTabs
{
    #[Locked]
    public ?string $activeTab = null;

    /** @var array<string, int>|null */
    protected ?array $tabCountsCache = null;

    public function selectTab(string $key): void
    {
        if (! $this->tabExists($key)) {
            return;
        }

        $this->resetPage();

        $this->activeTab = $key;
        $this->tabCountsCache = null;

        $this->renderTabsPartial();
    }

    public function applyDefaultTab(): void
    {
        if ($this->activeTab !== null) {
            return;
        }

        $config = $this->tabsConfig();

        if ($config?->default !== null && isset($config->tabs[$config->default])) {
            $this->activeTab = $config->default;
        }
    }

    public function hasTabs(): bool
    {
        $config = $this->tabsConfig();

        return $config !== null && count($config->tabs) > 0;
    }

    public function applyActiveTabScope(mixed $query): mixed
    {
        $tab = $this->activeTabModel();

        if ($tab === null) {
            return $query;
        }

        if (is_array($tab->scope) && $this->queryIsBuilder($query)) {
            $scope = $this->resolveTabScope($tab->scope);

            if ($scope !== null) {
                [$column, $operator, $value] = $scope;

                $operator === 'in'
                    ? $query->whereIn($column, (array) $value)
                    : $query->where($column, $operator, $value);
            }
        }

        return $this->tabQuery((string) $this->activeTab, $query);
    }

    /** @return array<string, mixed> */
    public function tabsData(): array
    {
        $config = $this->tabsConfig();

        if ($config === null) {
            return ['tableName' => $this->tableName, 'tabs' => [], 'activeTab' => null, 'align' => 'center'];
        }

        $counts = $this->computeTabCounts();

        $tabs = [];

        foreach ($config->tabs as $key => $tab) {
            $tabs[] = [
                'key' => $key,
                'label' => $tab->label,
                'icon' => $tab->icon,
                'active' => $this->activeTab === $key,
                'badge' => $this->resolveTabBadge($key, $tab, $counts),
            ];
        }

        return [
            'tableName' => $this->tableName,
            'tabs' => $tabs,
            'activeTab' => $this->activeTab,
            'align' => $this->tabsAlign(),
        ];
    }

    public function tabsAlign(): string
    {
        $config = $this->tabsConfig();

        return $config === null ? 'center' : $config->align;
    }

    /** @return view-string */
    public function tabsView(): string
    {
        $view = function_exists('theme_view') ? theme_view('tabs') : '';

        if ($view === '' || ! view()->exists($view)) {
            $view = $this->defaultTabsView();
        }

        /** @var view-string $view */
        return $view;
    }

    protected function defaultTabsView(): string
    {
        return 'powergrid-plugins::Tabs.themes.index';
    }

    public function renderTabsPartial(): void
    {
        if (! function_exists('partials') || ! $this->hasTabs()) {
            return;
        }

        partials($this)->partial("pg-tabs-{$this->tableName}", $this->tabsView(), $this->tabsData());

        $this->renderGridPartials();
    }

    protected function tabsConfig(): ?Tabs
    {
        $config = data_get($this->setUp, 'tabs');

        return $config instanceof Tabs ? $config : null;
    }

    protected function tabExists(string $key): bool
    {
        return isset($this->tabsConfig()?->tabs[$key]);
    }

    protected function activeTabModel(): ?Tab
    {
        if ($this->activeTab === null) {
            return null;
        }

        return $this->tabsConfig()?->tabs[$this->activeTab] ?? null;
    }

    /**
     * @param  array<string, int>  $counts
     */
    protected function resolveTabBadge(string $key, Tab $tab, array $counts): int|string|null
    {
        $custom = $this->tabBadge($key);

        if ($custom !== null) {
            return $custom;
        }

        return match (true) {
            $tab->badge === false => null,
            $tab->badge === true => $counts[$key] ?? null,
            default => $tab->badge,
        };
    }

    /**
     * @return array<string, int>
     */
    protected function computeTabCounts(): array
    {
        if ($this->tabCountsCache !== null) {
            return $this->tabCountsCache;
        }

        $config = $this->tabsConfig();

        if ($config === null || ! $this->readyToLoad) {
            return [];
        }

        $autoTabs = array_filter($config->tabs, fn (Tab $tab) => $tab->badge === true);

        if ($autoTabs === []) {
            return $this->tabCountsCache = [];
        }

        $datasource = $this->datasource([]);

        if (! $this->queryIsBuilder($datasource)) {
            return $this->tabCountsCache = [];
        }

        /** @var EloquentBuilder<Model>|QueryBuilder|Relation<Model, Model, mixed> $filtered */
        $filtered = app(Pipeline::class)
            ->send($datasource)
            ->through([new Filters($this)])
            ->thenReturn();

        $base = match (true) {
            $filtered instanceof QueryBuilder => clone $filtered,
            $filtered instanceof Relation => clone $filtered->getQuery()->getQuery(),
            default => clone $filtered->getQuery(),
        };

        $base->orders = null;
        $base->limit = null;
        $base->offset = null;
        $base->bindings['order'] = [];

        $grammar = $base->getGrammar();

        $selects = [];
        $bindings = [];
        $aliasMap = [];
        $index = 0;

        foreach ($autoTabs as $key => $tab) {
            $alias = 'pg_tab_'.$index++;
            $aliasMap[$alias] = $key;

            $scope = is_array($tab->scope) ? $this->resolveTabScope($tab->scope) : null;

            if ($scope === null) {
                $selects[] = "count(*) as {$alias}";

                continue;
            }

            [$column, $operator, $value] = $scope;
            $wrapped = $grammar->wrap($column);

            if ($operator === 'in') {
                $values = array_values((array) $value);
                $placeholders = implode(', ', array_fill(0, max(count($values), 1), '?'));
                $selects[] = "count(case when {$wrapped} in ({$placeholders}) then 1 end) as {$alias}";
                $bindings = array_merge($bindings, $values === [] ? [null] : $values);

                continue;
            }

            $selects[] = "count(case when {$wrapped} {$operator} ? then 1 end) as {$alias}";
            $bindings[] = $value;
        }

        /** @var object|null $row */
        $row = $base->selectRaw(implode(', ', $selects), $bindings)->first(); // @phpstan-ignore-line

        $counts = [];

        foreach ($aliasMap as $alias => $key) {
            $counts[$key] = $row !== null ? (int) ($row->{$alias} ?? 0) : 0;
        }

        return $this->tabCountsCache = $counts;
    }

    /**
     * @param  array<int, mixed>  $scope
     * @return array{0: string, 1: string, 2: mixed}|null
     */
    protected function resolveTabScope(array $scope): ?array
    {
        $scope = array_values($scope);

        if (count($scope) === 2) {
            $scope = [$scope[0], '=', $scope[1]];
        }

        if (count($scope) < 3) {
            return null;
        }

        $column = $scope[0];
        $operator = is_string($scope[1]) ? strtolower($scope[1]) : '';
        $value = $scope[2];

        $allowedOperators = ['=', '!=', '<>', '<', '>', '<=', '>=', 'like', 'not like', 'in'];

        if (! is_string($column) || ! in_array($operator, $allowedOperators, true)) {
            return null;
        }

        if (! in_array($column, $this->tabScopeAllowedFields(), true)) {
            return null;
        }

        return [$column, $operator, $value];
    }

    /** @return list<string> */
    protected function tabScopeAllowedFields(): array
    {
        $fields = [];

        foreach ($this->declaredColumns() as $column) {
            /** @var string|null $field */
            $field = data_get($column, 'dataField') ?: data_get($column, 'field');

            if (is_string($field) && $field !== '') {
                $fields[] = $field;
            }
        }

        return array_values(array_unique($fields));
    }

    protected function queryIsBuilder(mixed $query): bool
    {
        return $query instanceof EloquentBuilder
            || $query instanceof QueryBuilder
            || $query instanceof Relation;
    }
}
