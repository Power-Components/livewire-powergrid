<?php

namespace PowerComponents\LivewirePowerGrid\Concerns\Filters;

use Illuminate\Support\{Arr, Collection};
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Support\FilterKey;
use PowerComponents\Turbine\Components\Filters\FilterBase;

trait BindsFilterQueryString
{
    /** @return Collection<string, string> */
    public function listColumnForQueryString(): Collection
    {
        $columns = collect();

        collect($this->declaredColumns())
            ->ensure([Column::class])
            ->each(function ($column) use (&$columns) {
                if (filled($column->dataField)) {
                    $columns->put($column->dataField, $column->title ?: $column->dataField);
                }

                $columns->put($column->field, $column->title ?: $column->field);
            });

        return $columns;
    }

    /** Query-string paths per filter type: bag path => alias suffix. */
    private const array QUERY_STRING_PATHS = [
        'input_text' => ['.value' => '', '.op' => '_operator'],
        'number' => ['.value.start' => '_start', '.value.end' => '_end'],
    ];

    /**
     * @param  string  $prefix  Prefix each field in URL
     * @return array<string, array{as: string, except: string}>
     */
    protected function powerGridQueryString(string $prefix = ''): array
    {
        $queryString = [];

        foreach (Arr::dot($this->declaredFilters()) as $filter) {
            /** @var FilterBase $filter */
            /** @var string $field */
            $field = $filter->field;
            $as = (string) str($field)
                ->when(filled($prefix), fn ($c) => $c->prepend($prefix.'_'))
                ->replace('.', '_')
                ->replaceMatches('/\_+/', '_');

            $type = data_get($filter, 'key');
            $type = is_string($type) ? $type : '';

            if ($type === 'dynamic') {
                $model = $this->dynamicFilterWireModel($filter);

                if ($model !== null) {
                    $queryString[$model] = ['as' => $as, 'except' => ''];
                }

                continue;
            }

            $encoded = FilterKey::encode($field);

            foreach (self::QUERY_STRING_PATHS[$type] ?? ['.value' => ''] as $path => $suffix) {
                $queryString['filters.'.$encoded.$path] = ['as' => $as.$suffix, 'except' => ''];
            }
        }

        return $queryString;
    }

    /** The `wire:model` a FilterDynamic binds to, if it declares one. */
    private function dynamicFilterWireModel(mixed $filter): ?string
    {
        $wireModel = array_values(
            Arr::where(
                (array) data_get($filter, 'attributes'),
                fn ($value, $key) => str($key)->contains('wire:model')
            )
        );

        return is_string($wireModel[0] ?? null) ? $wireModel[0] : null;
    }
}
