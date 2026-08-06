<?php

namespace PowerComponents\LivewirePowerGrid\Concerns;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use PowerComponents\LivewirePowerGrid\Column;
use stdClass;

trait Summarize
{
    public bool $headerTotalColumn = false;

    public bool $footerTotalColumn = false;

    /**
     * Raw (unformatted) summary values keyed by "field.operation" / "custom.key".
     * Populated by the Summaries pipeline and consumed by hydrateSummaries().
     *
     * @var array<string, mixed>
     */
    public array $summaryValues = [];

    private function resolveSummarizeColumn(): void
    {
        collect($this->columns)
            ->each(function ($column) {
                $hasHeader = false;
                $hasFooter = false;

                foreach (['sum', 'count', 'min', 'avg', 'max'] as $operation) {
                    $hasHeader = $hasHeader || data_get($column, "properties.summarize.$operation.header");
                    $hasFooter = $hasFooter || data_get($column, "properties.summarize.$operation.footer");
                }

                /** @var array<string, array<string, mixed>>|null $custom */
                $custom = data_get($column, 'properties.summarize.custom');

                if (is_array($custom)) {
                    foreach ($custom as $meta) {
                        $hasHeader = $hasHeader || (bool) data_get($meta, 'header');
                        $hasFooter = $hasFooter || (bool) data_get($meta, 'footer');
                    }
                }

                $this->headerTotalColumn = $this->headerTotalColumn || $hasHeader;
                $this->footerTotalColumn = $this->footerTotalColumn || $hasFooter;
            });
    }

    public function hasSummarizeInColumns(): bool
    {
        return collect($this->columns)
            ->filter(function (array|stdClass|Column $column) { // @phpstan-ignore-line
                return data_get($column, 'properties.summarize');
            })->count() > 0;
    }

    /**
     * Apply the computed summary values onto the columns for rendering.
     *
     * Runs every render so totals survive a records cache-hit (where the
     * datasource pipeline is skipped): the values are read from the in-request
     * property first, then from the dedicated summaries cache.
     */
    public function hydrateSummaries(): void
    {
        if (! $this->hasSummarizeInColumns()) {
            return;
        }

        $raw = $this->summaryValues;

        if ($raw === [] && filled(data_get($this->setUp, 'cache.enabled'))) {
            $tag = $this->summariesCacheTag();
            $key = $this->summariesCacheKey();

            /** @var array<string, mixed> $raw */
            $raw = (Cache::supportsTags()
                ? Cache::tags($tag)->get($key)
                : Cache::get($tag.'-'.$key)) ?? [];
        }

        if ($raw === []) {
            return;
        }

        $this->applySummaryValues($raw);
    }

    /**
     * @param  array<string, mixed>  $raw
     */
    private function applySummaryValues(array $raw): void
    {
        /** @var list<mixed> $columns */
        $columns = collect($this->columns)
            ->map(function ($column) use ($raw) {
                $column = (object) $column;

                /** @var string $dataField */
                $dataField = data_get($column, 'dataField');
                /** @var string $rawField */
                $rawField = data_get($column, 'field');
                $field = $dataField ?: $rawField;

                foreach (['sum', 'count', 'avg', 'min', 'max'] as $operation) {
                    if (! data_get($column, "properties.summarize.{$operation}")) {
                        continue;
                    }

                    if (! array_key_exists("{$field}.{$operation}", $raw)) {
                        continue;
                    }

                    $value = $this->formatSummaryValue((object) $column, $operation, $raw["{$field}.{$operation}"]);
                    data_set($column, "properties.summarize_values.{$operation}", $value);
                }

                /** @var array<string, mixed>|null $custom */
                $custom = data_get($column, 'properties.summarize.custom');

                if (is_array($custom)) {
                    foreach (array_keys($custom) as $key) {
                        if (! array_key_exists("custom.{$key}", $raw)) {
                            continue;
                        }

                        data_set($column, "properties.summarize_values.custom.{$key}", $raw["custom.{$key}"]);
                    }
                }

                return $column;
            })
            ->toArray();

        $this->columns = $columns;

        unset($this->visibleColumns);
    }

    private function formatSummaryValue(object $column, string $summarizeMethod, mixed $value): mixed
    {
        $summarizeFormatTasks = $this->summarizeFormat();

        foreach ($summarizeFormatTasks as $field => $formattingClosure) {
            if (! str_contains($field, '.')) {
                continue;
            }

            $fieldName = Str::beforeLast($field, '.');
            $methods = Str::afterLast($field, '.');

            if (! in_array($fieldName, [data_get($column, 'field'), data_get($column, 'dataField')], true)) {
                continue;
            }

            $applyToMethods = Str::of($methods)
                ->replaceMatches('/\s+/', '')
                ->replace(['{', '}'], '')
                ->explode(',')
                ->all();

            if (in_array($summarizeMethod, $applyToMethods, true) && is_callable($formattingClosure)) {
                $value = $formattingClosure($value);
            }
        }

        return $value;
    }

    public function summariesCacheTag(): string
    {
        /** @var string $prefix */
        $prefix = data_get($this->setUp, 'cache.prefix', '');
        /** @var string $customTag */
        $customTag = data_get($this->setUp, 'cache.tag', '');

        if (filled($customTag)) {
            return $prefix.$customTag;
        }

        /** @var object|string $datasource */
        $datasource = $this->datasource();
        $table = is_object($datasource) && method_exists($datasource, 'getModel')
            ? $datasource->getModel()->getTable()
            : $this->tableName;

        return $prefix.'powergrid-'.$table.'-'.$this->tableName;
    }

    public function summariesCacheKey(): string
    {
        return 'pg-summaries-'.md5((string) json_encode([
            'search' => $this->search,
            'filters' => $this->filters,
            'softDeletes' => $this->softDeletes,
            'filterBuilder' => $this->filterBuilder,
        ]));
    }
}
