<?php

namespace PowerComponents\LivewirePowerGrid\Actions;

use Illuminate\Support\Facades\File;

use Symfony\Component\Finder\SplFileInfo;

final class ListModels
{
    /**
     * List files in Models
     *
     */
    public static function handle(): array
    {
        $directories = config('livewire-powergrid.auto_discover_models_paths', [app_path('Models')]);

        /** @var Array<int,string> $directories */
        return collect($directories)
            ->filter(fn (string $directory) => File::exists($directory))
            ->map(fn (string $directory) => File::allFiles($directory))
            ->flatten()
            ->reject(fn (SplFileInfo $file): bool => $file->getExtension() != 'php')

            // Get FQN Class from source code
            /** @phpstan-ignore-next-line */
            ->map(function (SplFileInfo $file): string {
                $sourceCode = strval(file_get_contents($file->getPathname()));

                return rescue(fn () => ParseFqnClassInCode::handle($sourceCode), '');
            })
            //Remove all unqualified PHP files code
            ->filter()

            // Remove classes that do not extend an Eloquent Model
            /** @phpstan-ignore-next-line */
            ->reject(fn (string $fqnClass) => rescue(fn () => (new \ReflectionClass($fqnClass))->isSubclassOf(\Illuminate\Database\Eloquent\Model::class), false) === false)
            ->toArray();
    }
}
