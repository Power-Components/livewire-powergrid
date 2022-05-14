<?php

namespace PowerComponents\LivewirePowerGrid\Commands\Actions;

use Illuminate\Support\Facades\File;

class Models
{
    /**
     * List files in Models folder
     *
     */
    public static function list(): array
    {
        $modelsFolder = app_path('Models');

        $files = collect(File::allFiles($modelsFolder))
            ->map(fn ($file) => $file->getFilenameWithoutExtension());

        $files->map(function ($file) use (&$files) {
            $files->push('App\\Models\\' . $file);
        });

        return  $files->toArray();
    }
}
