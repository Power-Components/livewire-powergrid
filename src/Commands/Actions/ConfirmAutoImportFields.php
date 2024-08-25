<?php

namespace PowerComponents\LivewirePowerGrid\Commands\Actions;

use function Laravel\Prompts\confirm;

final class ConfirmAutoImportFields
{
    public static function handle(string $label = 'Auto import fields?'): bool
    {
        return confirm(
            label: $label
        );
    }
}
