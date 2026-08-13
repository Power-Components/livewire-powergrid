<?php

namespace PowerComponents\LivewirePowerGrid\DataSource\Summaries;

use Closure;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Collection;
use PowerComponents\LivewirePowerGrid\{Column, Contracts\GridCache, Contracts\PowerGridContext};

/**
 * Computes the raw (unformatted) summary values for a datasource.
 *
 * Performance:
 *  - Database sources collapse every requested aggregate (sum/count/avg/min/max
 *    across all columns) into a SINGLE aggregate query instead of one query per
 *    column/operation.
 *  - Collection sources pluck each field ONCE and derive every operation from the
 *    same in-memory values.
 *  - When the component cache is enabled the raw map is cached keyed by the active
 *    filter/search state (independent of pagination/sort), so paging never recomputes.
 */
class SummaryCalculator
{
    /** @var list<string> */
    private const OPERATIONS = ['sum', 'count', 'avg', 'min', 'max'];

    public function __construct(private readonly PowerGridContext $component) {}

    /**
     * Compute the raw summary map for the given (already filtered) DataSource.
     * Keys are "field.operation" for built-ins and "custom.key" for closures.
     *
     * @return array<string, mixed>
     */
    public function compute(mixed $source): array
    {
        if (! $this->component->hasSummarizeInColumns()) {
            return [];
        }

        $resolver = fn (): array => $source instanceof Collection
            ? $this->fromCollection($source)
            : $this->fromQuery($source);

        if (! filled(data_get($this->component->state()->setUp, 'cache.enabled'))) {
            return $resolver();
        }

        return $this->remember($resolver);
    }

    /**
     * @return array<string, mixed>
     */
    private function fromQuery(mixed $query): array
    {
        [$aggregates, $customs] = $this->requests();

        $result = [];

        if ($aggregates !== []) {
            $base = $this->baseBuilder($query);
            $grammar = $base->getGrammar();

            $selects = [];
            $aliasMap = [];
            $index = 0;

            foreach ($aggregates as $key => [$operation, $field]) {
                $alias = 'pg_summary_'.$index++;
                $wrapped = $grammar->wrap($field);
                $selects[] = "{$operation}({$wrapped}) as {$alias}";
                $aliasMap[$alias] = $key;
            }

            $base->columns = null;
            $base->orders = null;
            $base->limit = null;
            $base->offset = null;
            $base->bindings['select'] = [];
            $base->bindings['order'] = [];

            /** @var object|null $row */
            $row = $base->selectRaw(implode(', ', $selects))->first(); // @phpstan-ignore-line

            foreach ($aliasMap as $alias => $key) {
                $result[$key] = $row?->{$alias};
            }
        }

        foreach ($customs as $key => $callback) {
            $result[$key] = $callback(is_object($query) ? clone $query : $query);
        }

        return $result;
    }

    /**
     * @param  Collection<int, mixed>  $collection
     * @return array<string, mixed>
     */
    private function fromCollection(Collection $collection): array
    {
        [$aggregates, $customs] = $this->requests();

        $result = [];

        /** @var array<string, list<array{0: string, 1: string}>> $byField */
        $byField = [];
        foreach ($aggregates as $key => [$operation, $field]) {
            $byField[$field][] = [$operation, $key];
        }

        foreach ($byField as $field => $operations) {
            $values = $collection->pluck($field);
            $numeric = $values->filter(fn ($value) => is_numeric($value));

            foreach ($operations as [$operation, $key]) {
                $result[$key] = match ($operation) {
                    'sum' => $numeric->sum(),
                    'count' => $collection->count(),
                    'avg' => $numeric->isNotEmpty() ? $numeric->avg() : null,
                    'min' => $numeric->min(),
                    'max' => $numeric->max(),
                    default => null,
                };
            }
        }

        foreach ($customs as $key => $callback) {
            $result[$key] = $callback(clone $collection);
        }

        return $result;
    }

    /**
     * Resolve every requested aggregate and custom summary across the columns.
     *
     * @return array{0: array<string, array{0: string, 1: string}>, 1: array<string, Closure>}
     */
    private function requests(): array
    {
        /** @var array<string, array{0: string, 1: string}> $aggregates */
        $aggregates = [];
        /** @var array<string, Closure> $customs */
        $customs = [];

        $callbacks = $this->customCallbacks();

        // The `columns` property is mass-assignable and hydrated from the client
        // snapshot, so it cannot be trusted. Resolve aggregates only from the
        // server-declared columns() method.
        foreach ($this->component->declaredColumns() as $column) {
            /** @var string $dataField */
            $dataField = data_get($column, 'dataField');
            /** @var string $rawField */
            $rawField = data_get($column, 'field');
            $field = $dataField ?: $rawField;

            foreach (self::OPERATIONS as $operation) {
                if (data_get($column, "properties.summarize.{$operation}")) {
                    $aggregates["{$field}.{$operation}"] = [$operation, $field];
                }
            }

            /** @var array<string, mixed>|null $custom */
            $custom = data_get($column, 'properties.summarize.custom');

            if (is_array($custom)) {
                foreach (array_keys($custom) as $key) {
                    $callback = $callbacks[$key] ?? null;

                    if ($callback instanceof Closure) {
                        $customs["custom.{$key}"] = $callback;
                    }
                }
            }
        }

        return [$aggregates, $customs];
    }

    /**
     * Re-resolve custom summary closures from the fresh columns() definition
     * (they are stripped from the serialized columns).
     *
     * @return array<string, Closure>
     */
    private function customCallbacks(): array
    {
        $declared = collect($this->component->declaredColumns());

        $hasCustom = $declared
            ->contains(fn ($column) => filled(data_get($column, 'properties.summarize.custom')));

        if (! $hasCustom) {
            return [];
        }

        $callbacks = [];

        foreach ($declared as $column) {
            if (! $column instanceof Column) {
                continue;
            }

            foreach ($column->summaryCallbacks as $key => $callback) {
                $callbacks[$key] = $callback;
            }
        }

        return $callbacks;
    }

    private function baseBuilder(mixed $query): QueryBuilder
    {
        // Always clone: when a model has no global scopes, EloquentBuilder::toBase()
        // returns the ORIGINAL underlying query (applyScopes() returns $this), and we
        // would mutate the very query the pipeline forwards to pagination.
        if ($query instanceof EloquentBuilder) {
            return (clone $query)->toBase();
        }

        if ($query instanceof Relation) {
            return (clone $query->getQuery())->toBase();
        }

        /** @var QueryBuilder $query */
        return clone $query;
    }

    /**
     * @param  Closure(): array<string, mixed>  $resolver
     * @return array<string, mixed>
     */
    private function remember(Closure $resolver): array
    {
        $tag = $this->component->summariesCacheTag();
        $key = $this->component->summariesCacheKey();
        /** @var int $ttl */
        $ttl = data_get($this->component->state()->setUp, 'cache.ttl', 300);

        $cache = app(GridCache::class);

        /** @var array<string, mixed> $values */
        $values = $cache->supportsTags()
            ? $cache->taggedRemember($tag, $key, $ttl, $resolver)
            : $cache->remember($tag.'-'.$key, $ttl, $resolver);

        return $values;
    }
}
