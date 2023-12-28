<?php

namespace PowerComponents\LivewirePowerGrid\Commands\Actions;

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

                if (in_array($columnType, ['datetime', 'date'])) {
                    $columns .= '            Column::make(\'' . $title . '\', \'' . $field . '_formatted\', \'' . $field . '\')' . "\n" . '                ->sortable(),' . "\n\n";
                }

                if ($columnType === 'datetime') {
                    $datasource .= "\n" . '            ->addColumn(\'' . $field . '_formatted\', fn (' . $modelUnqualifiedName . ' $model) => Carbon::parse($model->' . $field . ')->format(\'d/m/Y H:i:s\'))';
                    $filters .= '            Filter::datetimepicker(\'' . $field . '\'),' . "\n";

                    continue;
                }

                if ($columnType === 'date') {
                    $datasource .= "\n" . '            ->addColumn(\'' . $field . '_formatted\', fn (' . $modelUnqualifiedName . ' $model) => Carbon::parse($model->' . $field . ')->format(\'d/m/Y\'))';
                    $filters .= '            Filter::datepicker(\'' . $field . '\'),' . "\n";

                    continue;
                }

                if ($columnType === 'boolean') {
                    $datasource .= "\n" . '            ->addColumn(\'' . $field . '\')';
                    $columns .= '            Column::make(\'' . $title . '\', \'' . $field . '\')' . "\n" . '                ->toggleable(),' . "\n\n";
                    $filters .= '            Filter::boolean(\'' . $field . '\'),' . "\n";

                    continue;
                }

                if (in_array($columnType, ['smallint', 'integer', 'bigint'])) {
                    $datasource .= "\n" . '            ->addColumn(\'' . $field . '\')';
                    $columns .= '            Column::make(\'' . $title . '\', \'' . $field . '\'),' . "\n";

                    continue;
                }

                if ($columnType === 'string') {
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
     */
    public static function queryBuilder(string $databaseTableName, string $stubFile = null): string
    {
        $stub = empty($stubFile) ? File::get(__DIR__ . '/../../../resources/stubs/table.query-builder.fillable.stub') : File::get(base_path($stubFile));

        $columnListing = Schema::getColumnListing($databaseTableName);

        $datasource = '';
        $columns    = "[\n";
        $filters    = "[\n";

        $filteredColumns = collect($columnListing)
            ->filter(function ($column) {
                return !in_array($column, ['password', 'remember_token', 'email_verified_at']);
            })
            ->toArray();

        /** @var string $field */
        foreach ($filteredColumns as $field) {
            $columnType = Schema::getColumnType($databaseTableName, $field);

            $title = Str::of($field)->replace('_', ' ')->ucfirst();

            if (in_array($columnType, ['datetime', 'date'])) {
                $columns .= '            Column::make(\'' . $title . '\', \'' . $field . '_formatted\', \'' . $field . '\')' . "\n" . '                ->sortable(),' . "\n\n";
            }

            if ($columnType === 'datetime') {
                $datasource .= "\n" . '            ->addColumn(\'' . $field . '_formatted\', fn ($model) => Carbon::parse($model->' . $field . ')->format(\'d/m/Y H:i:s\'))';
                $filters .= '            Filter::datetimepicker(\'' . $field . '\'),' . "\n";

                continue;
            }

            if ($columnType === 'date') {
                $datasource .= "\n" . '            ->addColumn(\'' . $field . '_formatted\', fn ($model) => Carbon::parse($model->' . $field . ')->format(\'d/m/Y\'))';
                $filters .= '            Filter::datepicker(\'' . $field . '\'),' . "\n";

                continue;
            }

            if ($columnType === 'boolean') {
                $datasource .= "\n" . '            ->addColumn(\'' . $field . '\')';
                $columns .= '            Column::make(\'' . $title . '\', \'' . $field . '\')' . "\n" . '                ->toggleable(),' . "\n\n";
                $filters .= '            Filter::boolean(\'' . $field . '\'),' . "\n";

                continue;
            }

            if (in_array($columnType, ['smallint', 'integer', 'bigint'])) {
                $datasource .= "\n" . '            ->addColumn(\'' . $field . '\')';
                $columns .= '            Column::make(\'' . $title . '\', \'' . $field . '\'),' . "\n";

                continue;
            }

            if ($columnType === 'string') {
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
