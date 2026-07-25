<?php

namespace PowerComponents\LivewirePowerGrid\Plugins\FilterBuilder;

use Illuminate\Support\Collection;
use PowerComponents\LivewirePowerGrid\Components\Filters\{FilterBase, FilterInputText};
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

/**
 * Single source of truth for Filter Builder security & normalization.
 *
 * The applied state ($component->filterBuilder) is an untrusted, mass-assignable
 * public Livewire property. Every row is validated HERE — both when the user
 * clicks "Apply" and again while the query is being built — so a forged payload
 * (unknown column, bogus operator, injected match mode) can never reach the DB.
 */
final class FilterBuilderValidator
{
    /** Filter types the builder currently supports. */
    public const array SUPPORTED_TYPES = ['input_text', 'number', 'select', 'boolean', 'date', 'datetime'];

    /** Operators that take no value at all. */
    public const array VALUELESS_OPERATORS = [
        'is_empty', 'is_not_empty', 'is_null', 'is_not_null', 'is_blank', 'is_not_blank',
    ];

    /** Operators that require two values (value + value2). */
    public const array RANGE_OPERATORS = ['between'];

    /**
     * @return array<string, list<string>>
     */
    public static function operators(): array
    {
        return [
            'input_text' => array_values(FilterInputText::getInputTextOperators()),
            'number' => ['between', 'greater_equal', 'less_equal'],
            'select' => ['is'],
            'boolean' => ['is'],
            'date' => ['between'],
            'datetime' => ['between'],
        ];
    }

    /**
     * Build the column allowlist from filters()/columns(). This is the ONLY
     * source of filterable columns — nothing from the request is trusted.
     *
     * @return array<string, array{
     *     field: string, title: string, type: string,
     *     operators: list<string>, options: array<int, array{value: mixed, label: string}>,
     *     definition: FilterBase|null
     * }>
     */
    public static function columnsMeta(PowerGridComponent $component): array
    {
        $config = data_get($component->setUp, 'filterBuilder');

        /** @var list<string> $only */
        $only = (array) data_get($config, 'only', []);
        /** @var list<string> $except */
        $except = (array) data_get($config, 'except', []);

        $operatorsByType = self::operators();

        $titles = collect($component->columns)
            ->flatMap(function ($column) {
                /** @var string $field */
                $field = data_get($column, 'field', '');
                /** @var string $dataField */
                $dataField = data_get($column, 'dataField', '');
                /** @var string $title */
                $title = data_get($column, 'title', $field);

                $map = [$field => $title];

                if ($dataField !== '') {
                    $map[$dataField] = $title;
                }

                return $map;
            });

        $meta = [];

        foreach ($component->filters() as $definition) {
            /** @var string $type */
            $type = data_get($definition, 'key', '');
            /** @var string $field */
            $field = data_get($definition, 'field', '');
            /** @var string $column */
            $column = data_get($definition, 'column', '');

            if (! in_array($type, self::SUPPORTED_TYPES, true) || $field === '') {
                continue;
            }

            if (filled($only) && ! in_array($field, $only, true) && ! in_array($column, $only, true)) {
                continue;
            }

            if (in_array($field, $except, true) || in_array($column, $except, true)) {
                continue;
            }

            /** @var string $columnTitle */
            $columnTitle = $titles->get($field, $titles->get($column, $field));

            $meta[$field] = [
                'field' => $field,
                'title' => $columnTitle,
                'type' => $type,
                'operators' => $operatorsByType[$type] ?? [],
                'options' => self::optionsFor($definition),
                'definition' => $definition instanceof FilterBase ? $definition : null,
            ];
        }

        return $meta;
    }

    /**
     * Validate and normalize an incoming payload against the allowlist.
     * Invalid rows are silently dropped. Never throws on bad input.
     *
     * The AND/OR connector is per row (row.boolean); the first row's connector
     * is irrelevant (it is the base of the group). `match` is kept only as the
     * default connector the modal seeds new rows with.
     *
     * @param  array<string, array<string, mixed>>  $meta
     * @return array{match: string, rows: list<array{column: string, operator: string, value: mixed, value2: mixed, boolean: string}>}
     */
    public static function validate(mixed $payload, array $meta, int $maxConditions): array
    {
        $match = data_get($payload, 'match') === 'or' ? 'or' : 'and';

        /** @var array<int, mixed> $rawRows */
        $rawRows = (array) data_get($payload, 'rows', []);

        $rows = [];

        foreach ($rawRows as $rawRow) {
            $clean = self::normalizeRow($rawRow, $meta);

            if ($clean !== null) {
                $rows[] = $clean;
            }

            if (count($rows) >= max(1, $maxConditions)) {
                break;
            }
        }

        return ['match' => $match, 'rows' => $rows];
    }

    /**
     * @param  array<string, array<string, mixed>>  $meta
     * @return array{column: string, operator: string, value: mixed, value2: mixed, boolean: string}|null
     */
    private static function normalizeRow(mixed $rawRow, array $meta): ?array
    {
        /** @var string $column */
        $column = data_get($rawRow, 'column', '');
        /** @var string $operator */
        $operator = data_get($rawRow, 'operator', '');

        // 1) Column must exist in the server-declared allowlist (airtight).
        if (! array_key_exists($column, $meta)) {
            return null;
        }

        // 2) Defense-in-depth: reject identifiers with unexpected characters.
        if (! preg_match('/^[A-Za-z0-9_.]+$/', $column)) {
            return null;
        }

        // 3) Operator must be whitelisted for this column's type.
        /** @var list<string> $allowedOperators */
        $allowedOperators = (array) data_get($meta, "$column.operators", []);

        if (! in_array($operator, $allowedOperators, true)) {
            return null;
        }

        // 4) Connector is a strict two-value whitelist.
        $boolean = data_get($rawRow, 'boolean') === 'or' ? 'or' : 'and';

        $value = data_get($rawRow, 'value');
        $value2 = data_get($rawRow, 'value2');

        // 5) Value requirements per operator shape.
        if (in_array($operator, self::VALUELESS_OPERATORS, true)) {
            return ['column' => $column, 'operator' => $operator, 'value' => null, 'value2' => null, 'boolean' => $boolean];
        }

        if (in_array($operator, self::RANGE_OPERATORS, true)) {
            if (self::blankValue($value) || self::blankValue($value2)) {
                return null;
            }

            return ['column' => $column, 'operator' => $operator, 'value' => $value, 'value2' => $value2, 'boolean' => $boolean];
        }

        if (self::blankValue($value)) {
            return null;
        }

        return ['column' => $column, 'operator' => $operator, 'value' => $value, 'value2' => null, 'boolean' => $boolean];
    }

    private static function blankValue(mixed $value): bool
    {
        return $value === null || $value === '' || (is_array($value) && count($value) === 0);
    }

    /**
     * Resolve the value options shown in the modal for a column. Booleans expose
     * their true/false labels; selects resolve statically-known data sources
     * (array/Collection) — closures are skipped (the modal falls back to a
     * free-text value input, still validated). Other types have no options.
     *
     * @return array<int, array{value: mixed, label: string}>
     */
    private static function optionsFor(mixed $definition): array
    {
        /** @var string $key */
        $key = data_get($definition, 'key', '');

        if ($key === 'boolean') {
            /** @var string $trueLabel */
            $trueLabel = data_get($definition, 'trueLabel', 'Yes');
            /** @var string $falseLabel */
            $falseLabel = data_get($definition, 'falseLabel', 'No');

            return [
                ['value' => 'true', 'label' => $trueLabel],
                ['value' => 'false', 'label' => $falseLabel],
            ];
        }

        if ($key !== 'select') {
            return [];
        }

        $dataSource = data_get($definition, 'dataSource');
        /** @var string $optionValue */
        $optionValue = data_get($definition, 'optionValue', '');
        /** @var string $optionLabel */
        $optionLabel = data_get($definition, 'optionLabel', '');

        if ($optionValue === '' || $optionLabel === '') {
            return [];
        }

        if (! is_array($dataSource) && ! $dataSource instanceof Collection) {
            return [];
        }

        return collect($dataSource)
            ->map(function ($option) use ($optionValue, $optionLabel) {
                /** @var string $label */
                $label = data_get($option, $optionLabel, '');

                return [
                    'value' => data_get($option, $optionValue),
                    'label' => $label,
                ];
            })
            ->values()
            ->all();
    }
}
