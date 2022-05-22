<?php

namespace PowerComponents\LivewirePowerGrid\Commands\Actions;

use Illuminate\Support\Facades\File;

class Stubs
{
    /**
     * Load stub
     *
     */
    public static function load(string $creationModel, string $template = null): string
    {
        if (!empty($creationModel) && !empty($template)) {
            return File::get(base_path($template));
        }

        if (strtolower(trim($creationModel)) === 'm') {
            return File::get(__DIR__ . '/../../../resources/stubs/table.model.stub');
        }

        return File::get(__DIR__ . '/../../../resources/stubs/table.collection.stub');
    }
}
