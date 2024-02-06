<?php

namespace PowerComponents\LivewirePowerGrid\Actions;

use Illuminate\Support\Facades\File;

use Symfony\Component\Finder\SplFileInfo;

final class ListModels
{
    /**
     * List files in Models folder
     *
     */
    public static function handle(): array
    {
        $modelsFolder = app_path('Models');

        return collect(File::allFiles($modelsFolder))
            ->reject(fn (SplFileInfo $file): bool => $file->getExtension() != 'php')
            ->map(function (SplFileInfo $file): array {
                return [
                    'file' => $file->getFilenameWithoutExtension(),
                    'path' => 'App\\Models\\' . $file->getFilenameWithoutExtension(),
                ];
            })
            ->flatten()
            ->toArray();
    }
}
