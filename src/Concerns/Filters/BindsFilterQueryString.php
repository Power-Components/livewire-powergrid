<?php

namespace PowerComponents\LivewirePowerGrid\Concerns\Filters;

use Illuminate\Support\{Arr, Collection};
use PowerComponents\LivewirePowerGrid\Column;
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

        $columns = $this->listColumnForQueryString();

        foreach (Arr::dot($this->declaredFilters()) as $filter) {
            /** @var FilterBase $filter */
            /** @var string $field */
            $field = $filter->field;
            $as = str($field)
                ->when(filled($prefix), fn ($c) => $c->prepend($prefix.'_'))
                ->replace('.', '_')
                ->replaceMatches('/\_+/', '_');

            if (filled(request()->get($as))) {
                $this->addEnabledFilters($field, strval($columns->get($field, $field)));
            }

            /** @var string $key */
            $key = data_get($filter, 'key');

            if ($key === 'input_text') {
                $queryString['filters.input_text.'.$field] = [
                    'as' => $as->toString(),
                    'except' => '',
                ];

                $queryString['filters.input_text_options.'.$field] = [
                    'as' => $as->append('_operator')->toString(),
                    'except' => '',
                ];

                continue;
            }

            if ($key === 'number') {
                $_start = $as->append('_start')->toString();
                $_end = $as->append('_end')->toString();
                $fieldProcessed = false;

                $queryString['filters.number.'.$field.'.start'] = [
                    'as' => $_start,
                    'except' => '',
                ];

                if (filled(request()->get($_start))) {
                    $this->addEnabledFilters($field.'_start', strval($columns->get($field, $field)));

                    $fieldProcessed = true;
                }

                $queryString['filters.number.'.$field.'.end'] = [
                    'as' => $_end,
                    'except' => '',
                ];

                if ($fieldProcessed === false && filled(request()->get($_end))) {
                    $this->addEnabledFilters($field.'_end', strval($columns->get($field, $field)));
                }

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

            $queryString['filters.'.$key.'.'.$field] = [
                'as' => $as->toString(),
                'except' => '',
            ];
        }

        return $queryString;
    }
}
