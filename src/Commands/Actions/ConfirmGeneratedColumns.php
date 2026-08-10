<?php

namespace PowerComponents\LivewirePowerGrid\Commands\Actions;

use function Laravel\Prompts\confirm;

/** @codeCoverageIgnore */
final class ConfirmGeneratedColumns
{
    /** Asks whether the previewed fields should be generated, before anything is written to disk. */
    public static function handle(string $label = 'Generate the component with these fields?'): bool
    {
        return confirm(
            label: $label
        );
    }
}
