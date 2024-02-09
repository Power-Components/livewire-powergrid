<?php

namespace PowerComponents\LivewirePowerGrid\Actions;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use PowerComponents\LivewirePowerGrid\Support\PowerGridComponentMaker;

class GetStubVarsFromDbTable
{
    private static bool $hasEscapeExample = false;

    /**
     * @return array{'PowerGridFields': string, 'filters': string, 'columns': string}
     */
    public static function handle(PowerGridComponentMaker $component): array
    {
        $columnListing = Schema::getColumnListing($component->databaseTable);

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
            $columnType = Schema::getColumnType($component->databaseTable, $field);

            $title = Str::of($field)->replace('_', ' ')->ucfirst();

            if (in_array($columnType, ['datetime', 'date', 'timestamp'])) {
                $columns .= '            Column::make(\'' . $title . '\', \'' . $field . '_formatted\', \'' . $field . '\')' . "\n" . '                ->sortable(),' . "\n\n";
            }

            if ($columnType === 'datetime') {
                $datasource .= "\n" . '            ->add(\'' . $field . '_formatted\', fn ($model) => Carbon::parse($model->' . $field . ')->format(\'d/m/Y H:i:s\'))';
                $filters .= '            Filter::datetimepicker(\'' . $field . '\'),' . "\n";

                continue;
            }

            if ($columnType === 'date') {
                $datasource .= "\n" . '            ->add(\'' . $field . '_formatted\', fn ($model) => Carbon::parse($model->' . $field . ')->format(\'d/m/Y\'))';
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
                    $datasource .= "\n\n           /** Example of custom column using a closure **/\n" . '            ->add(\'' . $field . '_lower\', fn ($model) => strtolower(e($model->' . $field . ')))' . "\n";
                    self::$hasEscapeExample = true;
                }

                continue;
            }

            $datasource .= "\n" . '            ->add(\'' . $field . '\')';
            $columns .= '            Column::make(\'' . $title . '\', \'' . $field . '\')' . "\n" . '                ->sortable()' . "\n" . '                ->searchable(),' . "\n\n";
        }

        $columns .= "        ];";
        $filters .= "        ];";

        return [
            'PowerGridFields' => $datasource,
            'filters'         => $filters,
            'columns'         => $columns,
        ];
    }
}
