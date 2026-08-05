<?php

namespace PowerComponents\LivewirePowerGrid\Commands\Support;

use Illuminate\Support\Str;

final class StubColumnBuilder
{
    public const array SENSITIVE_FIELDS = ['password', 'remember_token', 'email_verified_at'];

    private bool $hasEscapeExample = false;

    private string $datasource = '';

    private string $columns = "[\n";

    private string $filters = "[\n";

    /**
     * @param  array<string, string>  $fieldTypes  map of field => column type
     * @return array{'PowerGridFields': string, 'filters': string, 'columns': string}
     */
    public function build(array $fieldTypes, string $modelTypeHint = '', bool $withActionColumn = true): array
    {
        $hint = $modelTypeHint !== '' ? $modelTypeHint.' ' : '';

        foreach ($fieldTypes as $field => $columnType) {
            $this->appendField(strval($field), $columnType, $hint);
        }

        if ($withActionColumn) {
            $this->columns .= '            Column::action(\'Action\')'."\n";
        }

        $this->columns .= '        ];';
        $this->filters .= '        ];';

        return [
            'PowerGridFields' => $this->datasource,
            'filters' => $this->filters,
            'columns' => $this->columns,
        ];
    }

    private function appendField(string $field, string $columnType, string $hint): void
    {
        $title = Str::of($field)->replace('_', ' ')->ucfirst();

        if (in_array($columnType, ['datetime', 'date', 'timestamp'])) {
            $this->columns .= '            Column::make(\''.$title.'\', \''.$field.'_formatted\', \''.$field.'\')'."\n".'                ->sortable(),'."\n\n";
        }

        if ($columnType === 'datetime') {
            $this->datasource .= "\n".'            ->add(\''.$field.'_formatted\', fn ('.$hint.'$model) => Carbon::parse($model->'.$field.')->format(\'d/m/Y H:i:s\'))';
            $this->filters .= '            Filter::datetimepicker(\''.$field.'\'),'."\n";

            return;
        }

        if ($columnType === 'date') {
            $this->datasource .= "\n".'            ->add(\''.$field.'_formatted\', fn ('.$hint.'$model) => Carbon::parse($model->'.$field.')->format(\'d/m/Y\'))';
            $this->filters .= '            Filter::datepicker(\''.$field.'\'),'."\n";

            return;
        }

        if ($columnType === 'boolean') {
            $this->datasource .= "\n".'            ->add(\''.$field.'\')';
            $this->columns .= '            Column::make(\''.$title.'\', \''.$field.'\')'."\n".'                ->toggleable(),'."\n\n";
            $this->filters .= '            Filter::boolean(\''.$field.'\'),'."\n";

            return;
        }

        if (in_array($columnType, ['smallint', 'integer', 'bigint'])) {
            $this->datasource .= "\n".'            ->add(\''.$field.'\')';
            $this->columns .= '            Column::make(\''.$title.'\', \''.$field.'\'),'."\n";

            return;
        }

        if ($columnType === 'string') {
            $this->datasource .= "\n".'            ->add(\''.$field.'\')';
            $this->columns .= '            Column::make(\''.$title.'\', \''.$field.'\')'."\n".'                ->sortable()'."\n".'                ->searchable(),'."\n\n";
            $this->filters .= '            Filter::inputText(\''.$field.'\')->operators([\'contains\']),'."\n";

            if (! $this->hasEscapeExample) {
                $this->datasource .= "\n\n           /** Example of custom column using a closure **/\n".'            ->add(\''.$field.'_lower\', fn ('.$hint.'$model) => strtolower(e($model->'.$field.')))'."\n";
                $this->hasEscapeExample = true;
            }

            return;
        }

        $this->datasource .= "\n".'            ->add(\''.$field.'\')';
        $this->columns .= '            Column::make(\''.$title.'\', \''.$field.'\')'."\n".'                ->sortable()'."\n".'                ->searchable(),'."\n\n";
    }
}
