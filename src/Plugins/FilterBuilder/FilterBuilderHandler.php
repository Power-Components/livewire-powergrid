<?php

namespace PowerComponents\LivewirePowerGrid\Plugins\FilterBuilder;

use Illuminate\Database\Eloquent\{Builder as EloquentBuilder, Model};
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Collection;
use PowerComponents\LivewirePowerGrid\Components\Filters\FilterBase;
use PowerComponents\LivewirePowerGrid\DataSource\Builders\{Boolean, DatePicker, DateTimePicker, InputText, Number, Select};
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class FilterBuilderHandler
{
    public function __construct(
        private readonly PowerGridComponent $component
    ) {}

    public function isActive(): bool
    {
        return filled(data_get($this->component->setUp, 'filterBuilder'));
    }

    /**
     * @param  EloquentBuilder<Model>|QueryBuilder  $query
     */
    public function apply(EloquentBuilder|QueryBuilder $query): void
    {
        $state = $this->state();

        if (empty($state['rows'])) {
            return;
        }

        /** @var EloquentBuilder<Model>|QueryBuilder $query */
        $query = $this->component->beforeFilterBuilderApply($query, $state);

        $meta = $this->meta();

        // Outer group isolates the (possibly OR) conditions so they never leak
        // into search or other filters ANDed at the top level. Each row joins the
        // previous ones with its own connector (row.boolean); SQL precedence makes
        // "A AND B OR C AND D" resolve as "(A AND B) OR (C AND D)".
        $query->where(function ($group) use ($state, $meta) {
            foreach ($state['rows'] as $i => $row) {
                /** @var string $type */
                $type = data_get($meta, "{$row['column']}.type");
                /** @var FilterBase|null $definition */
                $definition = data_get($meta, "{$row['column']}.definition");
                $shape = $this->shapeFor($type, $row);

                $method = ($i > 0 && ($row['boolean'] ?? 'and') === 'or') ? 'orWhere' : 'where';

                $group->{$method}(function ($sub) use ($type, $definition, $row, $shape) {
                    $this->builderFor($type, $definition)->builder($sub, $row['column'], $shape);
                });
            }
        });
    }

    /**
     * @param  Collection<int, mixed>  $collection
     * @return Collection<int, mixed>
     */
    public function applyCollection(Collection $collection): Collection
    {
        $state = $this->state();

        if (empty($state['rows'])) {
            return $collection;
        }

        /** @var Collection<int, mixed> $collection */
        $collection = $this->component->beforeFilterBuilderApply($collection, $state);

        $meta = $this->meta();

        // Mirror SQL precedence: split into OR-groups (a new group starts at each
        // row whose connector is OR), AND the rows within a group, OR the groups.
        $groups = $this->orGroups($state['rows']);

        if (count($groups) <= 1) {
            $results = $collection;

            foreach ($groups[0] ?? [] as $row) {
                $results = $this->applyRowToCollection($results, $row, $meta);
            }

            return $results->values();
        }

        /** @var string $primaryKey */
        $primaryKey = $this->component->realPrimaryKey;

        $matched = collect();

        foreach ($groups as $group) {
            $groupResults = $collection;

            foreach ($group as $row) {
                $groupResults = $this->applyRowToCollection($groupResults, $row, $meta);
            }

            $matched = $matched->concat($groupResults->all());
        }

        return $matched->unique($primaryKey)->values();
    }

    /**
     * @param  list<array{column: string, operator: string, value: mixed, value2: mixed, boolean: string}>  $rows
     * @return list<list<array{column: string, operator: string, value: mixed, value2: mixed, boolean: string}>>
     */
    private function orGroups(array $rows): array
    {
        $groups = [];
        $current = [];

        foreach ($rows as $i => $row) {
            if ($i > 0 && ($row['boolean'] ?? 'and') === 'or' && $current !== []) {
                $groups[] = $current;
                $current = [];
            }

            $current[] = $row;
        }

        if ($current !== []) {
            $groups[] = $current;
        }

        return $groups;
    }

    /**
     * @param  Collection<int, mixed>  $collection
     * @param  array{column: string, operator: string, value: mixed, value2: mixed, boolean: string}  $row
     * @param  array<string, array<string, mixed>>  $meta
     * @return Collection<int, mixed>
     */
    private function applyRowToCollection(Collection $collection, array $row, array $meta): Collection
    {
        /** @var string $type */
        $type = data_get($meta, "{$row['column']}.type");
        /** @var FilterBase|null $definition */
        $definition = data_get($meta, "{$row['column']}.definition");

        return $this->builderFor($type, $definition)
            ->collection($collection, $row['column'], $this->shapeFor($type, $row));
    }

    /**
     * @return array{match: string, rows: list<array{column: string, operator: string, value: mixed, value2: mixed, boolean: string}>}
     */
    private function state(): array
    {
        /** @var int|string $maxConditions */
        $maxConditions = data_get($this->component->setUp, 'filterBuilder.maxConditions', 30);

        return FilterBuilderValidator::validate(
            $this->component->filterBuilder,
            $this->meta(),
            intval($maxConditions),
        );
    }

    /** @return array<string, array<string, mixed>> */
    private function meta(): array
    {
        return FilterBuilderValidator::columnsMeta($this->component);
    }

    private function builderFor(string $type, ?FilterBase $definition): Boolean|DatePicker|DateTimePicker|InputText|Number|Select
    {
        return match ($type) {
            'input_text' => new InputText($this->component, $definition),
            'number' => new Number($this->component, $definition),
            'select' => new Select($this->component, $definition),
            'boolean' => new Boolean($this->component, $definition),
            'date' => new DatePicker($this->component, $definition),
            'datetime' => new DateTimePicker($this->component, $definition),
            default => new InputText($this->component, $definition),
        };
    }

    /**
     * @param  array{column: string, operator: string, value: mixed, value2: mixed, boolean: string}  $row
     * @return array<string, mixed>|int|string|null
     */
    private function shapeFor(string $type, array $row): array|int|string|null
    {
        return match ($type) {
            'input_text' => [
                'selected' => $row['operator'],
                'value' => $row['value'],
                'searchMorphs' => $this->component->searchMorphs(),
            ],
            'number' => match ($row['operator']) {
                'greater_equal' => ['start' => $row['value']],
                'less_equal' => ['end' => $row['value']],
                default => ['start' => $row['value'], 'end' => $row['value2']],
            },
            'date', 'datetime' => ['start' => $row['value'], 'end' => $row['value2']],
            default => $this->scalarValue($row['value']),
        };
    }

    private function scalarValue(mixed $value): int|string|null
    {
        if (is_int($value) || is_string($value) || is_null($value)) {
            return $value;
        }

        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        return is_float($value) ? strval($value) : null;
    }
}
