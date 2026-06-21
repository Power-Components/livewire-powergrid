<?php

namespace PowerComponents\LivewirePowerGrid\Commands\Actions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\Finder\SplFileInfo;

final class ListModels
{
    /**
     * List files in Models
     *
     * @return array<string, string>
     */
    public static function handle(): array
    {
        $directories = config('livewire-powergrid.auto_discover_models_paths', [app_path('Models')]);

        /** @var array<int,string> $directories */
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
            // Remove all unqualified PHP files code
            ->filter()

            // Remove classes that do not extend an Eloquent Model
            /**
             * @throws ReflectionException
             */
            ->reject(function (string $fqnClass) {
                /** @var class-string $fqnClass */
                return rescue(fn () => (new ReflectionClass($fqnClass))->isSubclassOf(Model::class), false) === false;
            })
            ->all();
    }
}
