<?php

namespace PowerComponents\LivewirePowerGrid\Actions;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use PowerComponents\LivewirePowerGrid\Support\PowerGridComponentMaker;

class GetStubVarsFromFromModel
{
    private static bool $hasEscapeExample = false;

    /**
     * @return array{'PowerGridFields': string, 'filters': string, 'columns': string}
     */
    public static function handle(PowerGridComponentMaker $component): array
    {
        /** @var  \Illuminate\Database\Eloquent\Model $model*/
        $model = new $component->modelFqn();

        $getFillable = $model->getFillable();

        if (filled($model->getKeyName())) {
            $getFillable = array_merge([$model->getKeyName()], $getFillable);
        }

        $getFillable = array_merge(
            $getFillable,
            ['created_at']
        );

        $datasource = '';
        $columns    = "[\n";
        $filters    = "[\n";

        foreach ($getFillable as $field) {
            if (in_array($field, $model->getHidden())) {
                continue;
            }

            $connection = Schema::connection($model->getConnection()->getName());

            $hasColumn = function () use ($model, $field, $connection) {
                try {
                    return $connection->hasColumn($model->getTable(), $field);
                } catch (\Exception) {
                    return Schema::hasColumn($model->getTable(), $field);
                }
            };

            if ($hasColumn()) {
                $columnType = $connection->getColumnType($model->getTable(), $field);

                $title = Str::of($field)->replace('_', ' ')->ucfirst();

                if (in_array($columnType, ['datetime', 'date', 'timestamp'])) {
                    $columns .= '            Column::make(\'' . $title . '\', \'' . $field . '_formatted\', \'' . $field . '\')' . "\n" . '                ->sortable(),' . "\n\n";
                }

                if ($columnType === 'datetime') {
                    $datasource .= "\n" . '            ->add(\'' . $field . '_formatted\', fn (' . $component->model . ' $model) => Carbon::parse($model->' . $field . ')->format(\'d/m/Y H:i:s\'))';
                    $filters .= '            Filter::datetimepicker(\'' . $field . '\'),' . "\n";

                    continue;
                }

                if ($columnType === 'date') {
                    $datasource .= "\n" . '            ->add(\'' . $field . '_formatted\', fn (' . $component->model . ' $model) => Carbon::parse($model->' . $field . ')->format(\'d/m/Y\'))';
                    $filters .= '            Filter::datepicker(\'' . $field . '\'),' . "\n";

                    continue;
                }

                if ($columnType === 'boolean') {
                    $datasource .= "\n" . '            ->add(\'' . $field . '\')';
                    $columns .= '            Column::make(\'' . $title . '\', \'' . $field . '\')' . "\n" . '                ->toggleable(),' . "\n\n";
                    $filters .= '            Filter::boolean(\'' . $field . '\'),' . "\n";

                    continue;
                }

                if (in_array($columnType, ['smallint', 'integer', 'bigint'])) {
                    $datasource .= "\n" . '            ->add(\'' . $field . '\')';
                    $columns .= '            Column::make(\'' . $title . '\', \'' . $field . '\'),' . "\n";

                    continue;
                }

                if ($columnType === 'string') {
                    $datasource .= "\n" . '            ->add(\'' . $field . '\')';
                    $columns .= '            Column::make(\'' . $title . '\', \'' . $field . '\')' . "\n" . '                ->sortable()' . "\n" . '                ->searchable(),' . "\n\n";
                    $filters .= '            Filter::inputText(\'' . $field . '\')->operators([\'contains\']),' . "\n";

                    if (!self::$hasEscapeExample) {
                        $datasource .= "\n\n           /** Example of custom column using a closure **/\n" . '            ->add(\'' . $field . '_lower\', fn (' . $component->model . ' $model) => strtolower(e($model->' . $field . ')))' . "\n";
                        self::$hasEscapeExample = true;
                    }

                    continue;
                }

                $datasource .= "\n" . '            ->add(\'' . $field . '\')';
                $columns .= '            Column::make(\'' . $title . '\', \'' . $field . '\')' . "\n" . '                ->sortable()' . "\n" . '                ->searchable(),' . "\n\n";
            }
        }

        $columns .= '            Column::action(\'Action\')' . "\n";

        $columns .= "        ];";
        $filters .= "        ];";

        return [
            'PowerGridFields' => $datasource,
            'filters'         => $filters,
            'columns'         => $columns,
        ];
    }
}
