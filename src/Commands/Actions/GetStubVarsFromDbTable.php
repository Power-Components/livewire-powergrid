<?php

namespace PowerComponents\LivewirePowerGrid\Commands\Actions;

use Illuminate\Support\Facades\Schema;
use PowerComponents\LivewirePowerGrid\Commands\Support\{PowerGridComponentMaker, StubColumnBuilder};

/** @codeCoverageIgnore */
class GetStubVarsFromDbTable
{
    /**
     * @return array{'PowerGridFields': string, 'filters': string, 'columns': string}
     */
    public static function handle(PowerGridComponentMaker $component): array
    {
        $fieldTypes = [];

        foreach (Schema::getColumnListing($component->databaseTable) as $field) {
            if (in_array($field, StubColumnBuilder::SENSITIVE_FIELDS)) {
                continue;
            }

            $fieldTypes[$field] = Schema::getColumnType($component->databaseTable, $field);
        }

        return (new StubColumnBuilder())->build($fieldTypes, '', false);
    }
}
