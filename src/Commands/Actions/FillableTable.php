<?php

namespace PowerComponents\LivewirePowerGrid\Commands\Actions;

use Illuminate\Support\Facades\{File, Schema};
use Illuminate\Support\Str;

class FillableTable
{
    private static bool $hasEscapeExample = false;

    /**
     * Creates PowerGrid columns based on Model's fillable property
     *
     * @param string $modelQualifiedName App\Models\User
     * @param string $modelUnqualifiedName User
     * @param string|null $stubFile
     * @return string
     * @throws \Exception
     */
    public static function create(string $modelQualifiedName, string $modelUnqualifiedName, string $stubFile = null): string
    {
        /** @var  \Illuminate\Database\Eloquent\Model $model*/
        $model = new $modelQualifiedName();

        if (!empty($stubFile)) {
            $stub =  File::get(base_path($stubFile));
        } else {
            $stub = File::get(__DIR__ . '/../../../resources/stubs/table.fillable.stub');
        }

        $getFillable = $model->getFillable();

        if (filled($model->getKeyName())) {
            $getFillable = array_merge([$model->getKeyName()], $getFillable);
        }

        $getFillable = array_merge(
            $getFillable,
            ['created_at', 'updated_at']
        );

        $datasource = '';
        $columns    = "[\n";

        foreach ($getFillable as $field) {
            if (in_array($field, $model->getHidden())) {
                continue;
            }

            $conn = Schema::getConnection();

            $conn->getDoctrineSchemaManager()
                ->getDatabasePlatform()
                ->registerDoctrineTypeMapping('enum', 'string');

            if (Schema::hasColumn($model->getTable(), $field)) {
                $column = $conn->getDoctrineColumn($model->getTable(), $field);

                $title = Str::of($field)->replace('_', ' ')->upper();

                if (in_array($column->getType()->getName(), ['datetime', 'date'])) {
                    $columns .= '            Column::make(\'' . $title . '\', \'' . $field . '_formatted\', \'' . $field . '\')' . "\n" . '                ->searchable()' . "\n" . '                ->sortable()' . "\n" . '                ->makeInputDatePicker(),' . "\n\n";
                }

                if ($column->getType()->getName() === 'datetime') {
                    $datasource .= "\n" . '            ->addColumn(\'' . $field . '_formatted\', fn (' . $modelUnqualifiedName . ' $model) => Carbon::parse($model->' . $field . ')->format(\'d/m/Y H:i:s\'))';

                    continue;
                }

                if ($column->getType()->getName() === 'date') {
                    $datasource .= "\n" . '            ->addColumn(\'' . $field . '_formatted\', fn (' . $modelUnqualifiedName . ' $model) => Carbon::parse($model->' . $field . ')->format(\'d/m/Y\'))';

                    continue;
                }

                if ($column->getType()->getName() === 'boolean') {
                    $datasource .= "\n" . '            ->addColumn(\'' . $field . '\')';
                    $columns    .= '            Column::make(\'' . $title . '\', \'' . $field . '\')' . "\n" . '                ->toggleable(),' . "\n\n";

                    continue;
                }

                if (in_array($column->getType()->getName(), ['smallint', 'integer', 'bigint'])) {
                    $datasource .= "\n" . '            ->addColumn(\'' . $field . '\')';
                    $columns    .= '            Column::make(\'' . $title . '\', \'' . $field . '\')' . "\n" . '                ->makeInputRange(),' . "\n\n";

                    continue;
                }

                if ($column->getType()->getName() === 'string') {
                    $datasource .= "\n" . '            ->addColumn(\'' . $field . '\')';
                    $columns    .= '            Column::make(\'' . $title . '\', \'' . $field . '\')' . "\n" . '                ->sortable()' . "\n" . '                ->searchable()' . "\n" . '                ->makeInputText(),' . "\n\n";

                    if (!self::$hasEscapeExample) {
                        $datasource .= "\n\n           /** Example of custom column using a closure **/\n" . '            ->addColumn(\'' . $field . '_lower\', function (' . $modelUnqualifiedName . ' $model) {
                return strtolower(e($model->' . $field . '));
            })' . "\n";
                        self::$hasEscapeExample = true;
                    }

                    continue;
                }

                $datasource .= "\n" . '            ->addColumn(\'' . $field . '\')';
                $columns    .= '            Column::make(\'' . $title . '\', \'' . $field . '\')' . "\n" . '                ->sortable()' . "\n" . '                ->searchable(),' . "\n\n";
            }
        }

        $columns .= "        ]\n";

        $stub = str_replace('{{ datasource }}', $datasource, $stub);

        return str_replace('{{ columns }}', $columns, $stub);
    }
}
