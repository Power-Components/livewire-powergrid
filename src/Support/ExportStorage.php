<?php

namespace PowerComponents\LivewirePowerGrid\Support;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PowerComponents\LivewirePowerGrid\Components\SetUp\Exportable;

final class ExportStorage
{
    public static function disk(Exportable|array $exportOptions): string
    {
        $disk = data_get($exportOptions, 'disk');

        if (blank($disk)) {
            $disk = data_get(config('livewire-powergrid.exportable.storage', []), 'disk');
        }

        if (blank($disk)) {
            $disk = config('filesystems.default');
        }

        return strval($disk);
    }

    public static function path(Exportable|array $exportOptions): string
    {
        $path = data_get($exportOptions, 'path');

        if (blank($path)) {
            $path = data_get(config('livewire-powergrid.exportable.storage', []), 'path', 'livewire-powergrid/exports');
        }

        return trim(strval($path), '/\\');
    }

    public static function filePath(Exportable|array $exportOptions, string $fileName): string
    {
        $path = self::path($exportOptions);
        $fileName = basename($fileName);

        return filled($path) ? $path.'/'.$fileName : $fileName;
    }

    public static function temporaryFile(string $extension): string
    {
        $directory = storage_path('framework/cache/livewire-powergrid/exports');

        if (! is_dir($directory)) {
            mkdir($directory, 0775, true);
        }

        return $directory.DIRECTORY_SEPARATOR.Str::uuid().'.'.ltrim($extension, '.');
    }

    public static function put(Exportable|array $exportOptions, string $fileName, string $localPath): string
    {
        $filePath = self::filePath($exportOptions, $fileName);
        $stream = fopen($localPath, 'r');

        try {
            Storage::disk(self::disk($exportOptions))->put($filePath, $stream);
        } finally {
            if (is_resource($stream)) {
                fclose($stream);
            }
        }

        return $filePath;
    }
}
