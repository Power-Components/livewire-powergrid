<?php

namespace PowerComponents\LivewirePowerGrid\Plugins\FilterBuilder;

use Illuminate\Support\Collection;
use PowerComponents\LivewirePowerGrid\Components\Filters\{FilterBase, FilterInputText};
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class FilterBuilderValidator
{
    public const array SUPPORTED_TYPES = ['input_text', 'number', 'select', 'boolean', 'date', 'datetime'];

    public const array VALUELESS_OPERATORS = [
        'is_empty', 'is_not_empty', 'is_null', 'is_not_null', 'is_blank', 'is_not_blank',
    ];

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
