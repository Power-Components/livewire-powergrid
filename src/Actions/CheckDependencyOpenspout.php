<?php

namespace PowerComponents\LivewirePowerGrid\Actions;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * Suggest to installopenspout if not installed.
 *
 */
final class CheckDependencyOpenspout
{
    public static function handle(): string
    {
        return self::checkIfFileContains(
            [
                base_path('composer.json'),
            ],
            'openspout/openspout',
        );
    }

    private static function failMessage(): string
    {
        return <<<EOD

<info>🤔 It looks like your project is missing a dependency!</info>

PowerGrid requires <comment>openspout/openspout</comment> to export table data.

Please, consider installing the package.

For more information, visit: <comment>https://livewire-powergrid.com/table/features-setup.html#exportable</comment>
EOD;
    }

    /**
     * @param array<int, string> $filesToCheck
     */
    private static function checkIfFileContains(array $filesToCheck, string $needle): string
    {
        foreach ($filesToCheck as $file) {
            if (File::exists($file) && !Str::contains(File::get($file), $needle)) {
                return self::failMessage();
            }
        }

        return '';
    }
}
