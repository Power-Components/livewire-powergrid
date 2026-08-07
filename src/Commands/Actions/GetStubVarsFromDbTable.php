<?php

namespace PowerComponents\LivewirePowerGrid\Commands\Actions;

use PowerComponents\LivewirePowerGrid\Commands\Support\{PowerGridComponentMaker, SchemaColumns};

/** @codeCoverageIgnore */
class GetStubVarsFromDbTable
{
    /**
     * @return array{'PowerGridFields': string, 'filters': string, 'columns': string}
     */
    public static function handle(PowerGridComponentMaker $component): array
    {
        $types = SchemaColumns::handle($component->databaseTable);

        $fields = $types->keys()
            ->reject(fn (string $field): bool => in_array($field, SchemaColumns::SENSITIVE_COLUMNS, true))
            ->values()
            ->all();

        return BuildStubVars::handle($fields, $types);
    }
}
