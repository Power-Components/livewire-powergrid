<?php

namespace PowerComponents\LivewirePowerGrid\Commands\Actions;

use Illuminate\Support\{Collection, Str};

/** Builds the fields, columns and filters stub variables from a set of typed columns. */
final class BuildStubVars
{
    private bool $hasEscapeExample = false;

    private string $fields = '';

    private string $columns = "[\n";

    private string $filters = "[\n";

    /**
     * @param  list<string>  $fields  Column names, in the order they should be generated.
     * @param  Collection<string, string>  $types  Column name => normalized type bucket.
     * @param  string  $model  Model class used to type-hint generated closures; empty leaves them untyped.
     * @return array{'PowerGridFields': string, 'filters': string, 'columns': string}
     */
    public static function handle(array $fields, Collection $types, string $model = ''): array
    {
        return (new self())->build($fields, $types, $model);
    }

    /**
     * @param  list<string>  $fields
     * @param  Collection<string, string>  $types
     * @return array{'PowerGridFields': string, 'filters': string, 'columns': string}
     */
    private function build(array $fields, Collection $types, string $model): array
    {
        $closure = $model === '' ? '$model' : $model.' $model';

        foreach ($fields as $field) {
            $type = $types->get($field);

            if ($type === null) {
                continue;
            }

            $title = Str::of($field)->replace('_', ' ')->ucfirst()->toString();

            match ($type) {
                'datetime' => $this->addDateTime($field, $title, $closure),
                'date' => $this->addDate($field, $title, $closure),
                'boolean' => $this->addBoolean($field, $title),
                'integer' => $this->addInteger($field, $title),
                'string' => $this->addString($field, $title, $closure),
                default => $this->addUntyped($field, $title),
            };
        }

        $this->columns .= '            Column::action(\'Action\')'."\n";

        return [
            'PowerGridFields' => $this->fields,
            'filters' => $this->filters.'        ];',
            'columns' => $this->columns.'        ];',
        ];
    }

    private function addDateTime(string $field, string $title, string $closure): void
    {
        $this->columns .= '            Column::make(\''.$title.'\', \''.$field.'_formatted\', \''.$field.'\')'."\n".'                ->sortable(),'."\n\n";
        $this->fields .= "\n".'            ->add(\''.$field.'_formatted\', fn ('.$closure.') => Carbon::parse($model->'.$field.')->format(\'d/m/Y H:i:s\'))';
        $this->filters .= '            Filter::datetimepicker(\''.$field.'\'),'."\n";
    }

    private function addDate(string $field, string $title, string $closure): void
    {
        $this->columns .= '            Column::make(\''.$title.'\', \''.$field.'_formatted\', \''.$field.'\')'."\n".'                ->sortable(),'."\n\n";
        $this->fields .= "\n".'            ->add(\''.$field.'_formatted\', fn ('.$closure.') => Carbon::parse($model->'.$field.')->format(\'d/m/Y\'))';
        $this->filters .= '            Filter::datepicker(\''.$field.'\'),'."\n";
    }

    private function addBoolean(string $field, string $title): void
    {
        $this->fields .= "\n".'            ->add(\''.$field.'\')';
        $this->columns .= '            Column::make(\''.$title.'\', \''.$field.'\')'."\n".'                ->toggleable(),'."\n\n";
        $this->filters .= '            Filter::boolean(\''.$field.'\'),'."\n";
    }

    private function addInteger(string $field, string $title): void
    {
        $this->fields .= "\n".'            ->add(\''.$field.'\')';
        $this->columns .= '            Column::make(\''.$title.'\', \''.$field.'\'),'."\n";
    }

    private function addString(string $field, string $title, string $closure): void
    {
        $this->fields .= "\n".'            ->add(\''.$field.'\')';
        $this->columns .= '            Column::make(\''.$title.'\', \''.$field.'\')'."\n".'                ->sortable()'."\n".'                ->searchable(),'."\n\n";
        $this->filters .= '            Filter::inputText(\''.$field.'\')->operators([\'contains\']),'."\n";

        if (! $this->hasEscapeExample) {
            $this->fields .= "\n\n           /** Example of custom column using a closure **/\n".'            ->add(\''.$field.'_lower\', fn ('.$closure.') => strtolower(e($model->'.$field.')))'."\n";
            $this->hasEscapeExample = true;
        }
    }

    /** Fallback for column types the generator has no opinion about: sortable and searchable, no filter. */
    private function addUntyped(string $field, string $title): void
    {
        $this->fields .= "\n".'            ->add(\''.$field.'\')';
        $this->columns .= '            Column::make(\''.$title.'\', \''.$field.'\')'."\n".'                ->sortable()'."\n".'                ->searchable(),'."\n\n";
    }
}
