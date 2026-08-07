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

        $fields = SchemaColumns::publicFields($types);

        return BuildStubVars::handle($fields, $types);
    }
}
