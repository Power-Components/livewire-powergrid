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
            $encoded = FilterKey::encode($field);
            $as = str($field)
                ->when(filled($prefix), fn ($c) => $c->prepend($prefix.'_'))
                ->replace('.', '_')
                ->replaceMatches('/\_+/', '_');

            /** @var string $key */
            $key = data_get($filter, 'key');

            if ($key === 'input_text') {
                $queryString['filters.'.$encoded.'.value'] = [
                    'as' => $as->toString(),
                    'except' => '',
                ];

                $queryString['filters.'.$encoded.'.op'] = [
                    'as' => $as->append('_operator')->toString(),
                    'except' => '',
                ];

                continue;
            }

            if ($key === 'number') {
                $queryString['filters.'.$encoded.'.value.start'] = [
                    'as' => $as->append('_start')->toString(),
                    'except' => '',
                ];

                $queryString['filters.'.$encoded.'.value.end'] = [
                    'as' => $as->append('_end')->toString(),
                    'except' => '',
                ];

                continue;
            }

            if ($key === 'dynamic') {
                $wireModel = array_values(
                    Arr::where(
                        (array) data_get($filter, 'attributes'),
                        fn ($value, $key) => str($key)->contains('wire:model')
                    )
                );

                if (count($wireModel) && is_string($wireModel[0])) {
                    $queryString[$wireModel[0]] = [
                        'as' => $as->toString(),
                        'except' => '',
                    ];
                }

                continue;
            }

            $queryString['filters.'.$encoded.'.value'] = [
                'as' => $as->toString(),
                'except' => '',
            ];
        }

        return $queryString;
    }
}
