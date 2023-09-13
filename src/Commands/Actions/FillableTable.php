<?php

namespace PowerComponents\LivewirePowerGrid\Commands\Actions;

use Doctrine\DBAL\Exception;
use Illuminate\Support\Facades\{File, Schema};
use Illuminate\Support\Str;

class FillableTable
{
    private static bool $hasEscapeExample = false;

    /**
     * Creates PowerGrid columns based on Builder's fillable property
     *
     * @param string $modelQualifiedName App\Models\User
     * @param string $modelUnqualifiedName User
     * @param string|null $stubFile
     * @return string
     * @throws \Exception
     */
    public static function eloquentBuilder(string $modelQualifiedName, string $modelUnqualifiedName, string $stubFile = null): string
    {
        /** @var  \Illuminate\Database\Eloquent\Model $model*/
        $model = new $modelQualifiedName();

        if (!empty($stubFile)) {
            $stub = File::get(base_path($stubFile));
        } else {
            $stub = File::get(__DIR__ . '/../../../resources/stubs/table.fillable.stub');
        }

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

            $conn = $model->getConnection();

            $conn->getDoctrineSchemaManager()
                ->getDatabasePlatform()
                ->registerDoctrineTypeMapping('enum', 'string');

            $hasColumn = function () use ($model, $field, $conn) {
                try {
                    return Schema::connection($conn->getDatabaseName())
                        ->hasColumn($model->getTable(), $field);
                } catch (\Exception) {
                    return Schema::hasColumn($model->getTable(), $field);
                }
            };

            if ($hasColumn()) {
                $column = $conn->getDoctrineColumn($model->getTable(), $field);

                $title = Str::of($field)->replace('_', ' ')->ucfirst();

                if (in_array($column->getType()->getName(), ['datetime', 'date'])) {
                    $columns .= '            Column::make(\'' . $title . '\', \'' . $field . '_formatted\', \'' . $field . '\')' . "\n" . '                ->sortable(),' . "\n\n";
                }

                if ($column->getType()->getName() === 'datetime') {
                    $datasource .= "\n" . '            ->addColumn(\'' . $field . '_formatted\', fn (' . $modelUnqualifiedName . ' $model) => Carbon::parse($model->' . $field . ')->format(\'d/m/Y H:i:s\'))';
                    $filters .= '            Filter::datetimepicker(\'' . $field . '\'),' . "\n";

                    continue;
                }

                if ($column->getType()->getName() === 'date') {
                    $datasource .= "\n" . '            ->addColumn(\'' . $field . '_formatted\', fn (' . $modelUnqualifiedName . ' $model) => Carbon::parse($model->' . $field . ')->format(\'d/m/Y\'))';
                    $filters .= '            Filter::datepicker(\'' . $field . '\'),' . "\n";

                    continue;
                }

                if ($column->getType()->getName() === 'boolean') {
                    $datasource .= "\n" . '            ->addColumn(\'' . $field . '\')';
                    $columns .= '            Column::make(\'' . $title . '\', \'' . $field . '\')' . "\n" . '                ->toggleable(),' . "\n\n";
                    $filters .= '            Filter::boolean(\'' . $field . '\'),' . "\n";

                    continue;
                }

                if (in_array($column->getType()->getName(), ['smallint', 'integer', 'bigint'])) {
                    $datasource .= "\n" . '            ->addColumn(\'' . $field . '\')';
                    $columns .= '            Column::make(\'' . $title . '\', \'' . $field . '\'),' . "\n";

                    continue;
                }

                if ($column->getType()->getName() === 'string') {
                    $datasource .= "\n" . '            ->addColumn(\'' . $field . '\')';
                    $columns .= '            Column::make(\'' . $title . '\', \'' . $field . '\')' . "\n" . '                ->sortable()' . "\n" . '                ->searchable(),' . "\n\n";
                    $filters .= '            Filter::inputText(\'' . $field . '\')->operators([\'contains\']),' . "\n";

                    if (!self::$hasEscapeExample) {
                        $datasource .= "\n\n           /** Example of custom column using a closure **/\n" . '            ->addColumn(\'' . $field . '_lower\', fn (' . $modelUnqualifiedName . ' $model) => strtolower(e($model->' . $field . ')))' . "\n";
                        self::$hasEscapeExample = true;
                    }

                    continue;
                }

                $datasource .= "\n" . '            ->addColumn(\'' . $field . '\')';
                $columns .= '            Column::make(\'' . $title . '\', \'' . $field . '\')' . "\n" . '                ->sortable()' . "\n" . '                ->searchable(),' . "\n\n";
            }
        }

        $columns .= '            Column::action(\'Action\')' . "\n";

        $columns .= "        ];";
        $filters .= "        ];";

        $stub = str_replace('{{ datasource }}', $datasource, $stub);

        $stub = str_replace('{{ filters }}', $filters, $stub);

        return str_replace('{{ columns }}', $columns, $stub);
    }

    /**
     * Creates PowerGrid columns based on Query Builder
     *
     * @param string $databaseTableName
     * @param string|null $stubFile
     * @return string
     * @throws Exception
     */
    public static function queryBuilder(string $databaseTableName, string $stubFile = null): string
    {
        $stub = empty($stubFile) ? File::get(__DIR__ . '/../../../resources/stubs/table.query-builder.fillable.stub') : File::get(base_path($stubFile));

        $columnListing = Schema::getColumnListing($databaseTableName);

        $datasource = '';
        $columns    = "[\n";
        $filters    = "[\n";

        foreach ($columnListing as $field) {
            $conn = Schema::getConnection();

            $conn->getDoctrineSchemaManager()
                ->getDatabasePlatform()
                ->registerDoctrineTypeMapping('enum', 'string');

            $column = $conn->getDoctrineColumn($databaseTableName, $field);

            $title = Str::of($field)->replace('_', ' ')->ucfirst();

            if (in_array($column->getType()->getName(), ['datetime', 'date'])) {
                $columns .= '            Column::make(\'' . $title . '\', \'' . $field . '_formatted\', \'' . $field . '\')' . "\n" . '                ->sortable(),' . "\n\n";
            }

            if ($column->getType()->getName() === 'datetime') {
                $datasource .= "\n" . '            ->addColumn(\'' . $field . '_formatted\', fn ($model) => Carbon::parse($model->' . $field . ')->format(\'d/m/Y H:i:s\'))';
                $filters .= '            Filter::datetimepicker(\'' . $field . '\'),' . "\n";

                continue;
            }

            if ($column->getType()->getName() === 'date') {
                $datasource .= "\n" . '            ->addColumn(\'' . $field . '_formatted\', fn ($model) => Carbon::parse($model->' . $field . ')->format(\'d/m/Y\'))';
                $filters .= '            Filter::datepicker(\'' . $field . '\'),' . "\n";

                continue;
            }

            if ($column->getType()->getName() === 'boolean') {
                $datasource .= "\n" . '            ->addColumn(\'' . $field . '\')';
                $columns .= '            Column::make(\'' . $title . '\', \'' . $field . '\')' . "\n" . '                ->toggleable(),' . "\n\n";
                $filters .= '            Filter::boolean(\'' . $field . '\'),' . "\n";

                continue;
            }

            if (in_array($column->getType()->getName(), ['smallint', 'integer', 'bigint'])) {
                $datasource .= "\n" . '            ->addColumn(\'' . $field . '\')';
                $columns .= '            Column::make(\'' . $title . '\', \'' . $field . '\'),' . "\n";

                continue;
            }

            if ($column->getType()->getName() === 'string') {
                $datasource .= "\n" . '            ->addColumn(\'' . $field . '\')';
                $columns .= '            Column::make(\'' . $title . '\', \'' . $field . '\')' . "\n" . '                ->sortable()' . "\n" . '                ->searchable(),' . "\n\n";
                $filters .= '            Filter::inputText(\'' . $field . '\')->operators([\'contains\']),' . "\n";

                if (!self::$hasEscapeExample) {
                    $datasource .= "\n\n           /** Example of custom column using a closure **/\n" . '            ->addColumn(\'' . $field . '_lower\', fn ($model) => strtolower(e($model->' . $field . ')))' . "\n";
                    self::$hasEscapeExample = true;
                }

                continue;
            }

            $datasource .= "\n" . '            ->addColumn(\'' . $field . '\')';
            $columns .= '            Column::make(\'' . $title . '\', \'' . $field . '\')' . "\n" . '                ->sortable()' . "\n" . '                ->searchable(),' . "\n\n";
        }

        $columns .= "        ];";
        $filters .= "        ];";

        $stub = str_replace('{{ datasource }}', $datasource, $stub);

        $stub = str_replace('{{ filters }}', $filters, $stub);

        return str_replace('{{ columns }}', $columns, $stub);
    }
}
