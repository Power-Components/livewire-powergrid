<?php

namespace PowerComponents\LivewirePowerGrid\Commands\Actions;

use PowerComponents\LivewirePowerGrid\Commands\Enums\ColumnSource;

use function Laravel\Prompts\select;

/** @codeCoverageIgnore */
final class AskColumnSource
{
    /** Asks where the generated fields should come from and returns the chosen ColumnSource case name. */
    public static function handle(string $model, string $databaseTable): string
    {
        // Must pass options as array<int, "label"> to
        // improve users experience when Laravel prompt falls back.
        $options = collect([
            ColumnSource::FILLABLE->name => '$fillable in ['.$model.'] Model',
            ColumnSource::DATABASE_TABLE->name => 'Columns in ['.$databaseTable.'] DB table',
        ]);

        $choice = strval(select(
            label: 'Where should the fields come from?',
            options: $options->values()->toArray(), // @phpstan-ignore-line
            default: 0
        ));

        return (string) $options->filter(fn (string $item): bool => $item === $choice)->keys()[0];
    }
}
