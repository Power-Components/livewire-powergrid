<?php

namespace PowerComponents\LivewirePowerGrid\Commands\Actions;

use PowerComponents\LivewirePowerGrid\Commands\Support\PowerGridComponentMaker;

/** @codeCoverageIgnore */
class GetStubVarsFromFromModel
{
    /**
     * @return array{'PowerGridFields': string, 'filters': string, 'columns': string}
     */
    public static function handle(PowerGridComponentMaker $component): array
    {
        $columns = ResolveGeneratedColumns::handle($component);

        return BuildStubVars::handle(array_values($columns->keys()->all()), $columns, $component->model);
    }
}
