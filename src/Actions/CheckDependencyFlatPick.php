<?php

namespace PowerComponents\LivewirePowerGrid\Actions;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * Suggest to Flatpick if not installed.
 *
 */

class CheckDependencyFlatPick
{
    public static function handle(): string
    {
        return self::checkIfFileContains(
            [
                base_path('package.json'),
            ],
            '"flatpickr"',
        );
    }

    private static function failMessage(): string
    {
        return <<<EOD

<info>🤔 It looks like your project is missing a dependency!</info>

PowerGrid requires <comment>flatpickr</comment> for Date filters to work.

Please, consider installing the package.

For more information, visit: <comment>https://livewire-powergrid.com/table/column-filters.html#filter-datetimepicker</comment>
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
