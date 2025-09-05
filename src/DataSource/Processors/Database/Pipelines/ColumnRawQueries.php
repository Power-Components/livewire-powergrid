<?php

namespace PowerComponents\LivewirePowerGrid\DataSource\Processors\Database\Pipelines;

use Closure;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Query\Builder as QueryBuilder;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

class ColumnRawQueries
{
    public function __construct(protected PowerGridComponent $component) {}

    public function handle(mixed $query, Closure $next): mixed
    {
        if (! ($query instanceof EloquentBuilder || $query instanceof MorphToMany || $query instanceof QueryBuilder)) {
            return $next($query);
        }

        collect($this->component->columns())
            ->filter(fn ($column) => filled(data_get($column, 'rawQueries')))
            ->each(function ($column) use ($query) {
                foreach ((array) data_get($column, 'rawQueries', []) as $rawQueryConfig) {
                    $this->executeRawQuery($query, $rawQueryConfig);
                }
            });

        return $next($query);
    }

    private function executeRawQuery(mixed $query, array $rawQueryConfig): void
    {
        $isEnabled = data_get($rawQueryConfig, 'enabled', true);

        if ($isEnabled instanceof Closure && ! $isEnabled($this->component)) {
            return;
        }

        if (! $isEnabled) {
            return;
        }

        $sql = data_get($rawQueryConfig, 'sql');
        $bindings = data_get($rawQueryConfig, 'bindings', []);
        $method = data_get($rawQueryConfig, 'method', 'whereRaw');

        $resolvedSql = $this->resolvePlaceholders($sql);
        $resolvedBindings = $this->resolveBindings($bindings);

        if ($resolvedSql) {
            $query->{$method}($resolvedSql, $resolvedBindings);
        }
    }

    private function resolvePlaceholders(?string $sql): ?string
    {
        if (is_null($sql)) {
            return null;
        }

        return preg_replace_callback('/\{(\w+)\}/', function ($matches) {
            $property = trim($matches[1]);

            return data_get($this->component, $property, '');
        }, $sql);
    }

    private function resolveBindings(array $bindings): array
    {
        return array_map(function ($param) {
            if ($param instanceof Closure) {
                return $param($this->component);
            }

            if (is_string($param)) {
                return $this->resolvePlaceholders($param);
            }

            return $param;
        }, $bindings);
    }
}
